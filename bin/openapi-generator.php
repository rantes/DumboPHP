#!/usr/bin/php
<?php
namespace OpenAPIGenerator;

class Generator {
    private string $_controllersPath;
    private string $_overridesPath;
    private string $_outputPath;
    private array $_excludeControllers;

    public function __construct(
        string $controllersPath,
        string $overridesPath,
        string $outputPath,
        array $excludeControllers = []
    ) {
        $this->_controllersPath    = $controllersPath;
        $this->_overridesPath      = $overridesPath;
        $this->_outputPath         = $outputPath;
        $this->_excludeControllers = $excludeControllers;
    }

    public function generate(): void {
        $skeleton  = $this->_buildSkeleton();
        $overrides = $this->_loadOverrides();
        $merged    = $this->_merge($skeleton, $overrides);

        file_put_contents(
            $this->_outputPath,
            json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        fwrite(STDOUT, "api.json generado en {$this->_outputPath}\n");
    }

    private function _buildSkeleton(): array {
        $paths = [];
        $files = glob("{$this->_controllersPath}/*.php");

        foreach ($files as $file):
            $controllerName = $this->_extractControllerName($file);

            if (in_array($controllerName, $this->_excludeControllers, true)):
                continue;
            endif;

            $actions = $this->_extractActions(
                file_get_contents($file)
            );

            foreach ($actions as $action):
                $route = $this->_actionToRoute(
                    $controllerName, $action['name']
                );
                $methods = $action['methods'];

                $paths[$route] ??= [];
                foreach ($methods as $method):
                    $paths[$route][$method] = [
                        'summary'     => "{$controllerName}::{$action['name']}",
                        'operationId' => "{$controllerName}_{$action['name']}",
                        'responses'   => [
                            '200' => ['description' => 'OK'],
                        ],
                    ];
                endforeach;
            endforeach;
        endforeach;

        return [
            'openapi' => '3.0.3',
            'info'    => [
                'title'   => 'Generado automáticamente',
                'version' => '1.0.0',
            ],
            'paths' => $paths,
        ];
    }

    private function _extractControllerName(string $file): string {
        $base = basename($file, '.php');
        // admin_controller.php → admin
        return str_replace('_controller', '', $base);
    }

    /**
     * Detecta métodos públicos *Action() y, dentro de
     * cada uno, intenta inferir qué verbos HTTP maneja
     * buscando comparaciones contra
     * $_SERVER['REQUEST_METHOD'].
     */
    private function _extractActions(string $source): array {
        $actions = [];

        preg_match_all(
            '/public function (\w+)Action\s*\([^)]*\)[^{]*\{/',
            $source, $matches, PREG_OFFSET_CAPTURE
        );

        foreach ($matches[1] as $i => $match):
            $name = $match[0];
            // Posición justo después de la '{' de apertura
            // (offset del match completo + su longitud)
            $braceOpenPos = $matches[0][$i][1]
                + strlen($matches[0][$i][0]) - 1;

            $body = $this->_extractBalancedBody(
                $source, $braceOpenPos
            );

            $methods = $this->_inferHttpMethods($body);

            $actions[] = [
                'name'    => $name,
                'methods' => $methods,
            ];
        endforeach;

        return $actions;
    }

    /**
     * Extrae el cuerpo de un método contando llaves { }
     * balanceadas desde la apertura — reemplaza la
     * ventana fija de 2000 caracteres que se derramaba
     * hacia el método siguiente (confirmado: fabricaba
     * verbos HTTP falsos en métodos cortos seguidos de
     * uno con switch sobre REQUEST_METHOD).
     *
     * No es un parser PHP real (no distingue llaves
     * dentro de strings/comentarios) — suficiente para
     * el propósito de esta heurística v2, más preciso
     * que la ventana fija anterior.
     */
    private function _extractBalancedBody(
        string $source, int $openBracePos
    ): string {
        $depth = 1;
        $pos   = $openBracePos + 1;
        $len   = strlen($source);

        while ($depth > 0 && $pos < $len):
            $char = $source[$pos];
            $char === '{' and $depth++;
            $char === '}' and $depth--;
            $pos++;
        endwhile;

        return substr($source, $openBracePos, $pos - $openBracePos);
    }

    private function _inferHttpMethods(string $body): array {
        $found = [];
        $candidates = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];

        foreach ($candidates as $verb):
            $isDetected = false;

            // Patrón 1 — comparación directa (=== / == / !=).
            // Nunca cuenta un fallback tras ?? (ej: $x ?? 'GET'
            // NO cuenta, pero $x === 'GET' SÍ cuenta).
            $comparisonPattern = '/(?:={2,3}|!={1,2})\s*[\'"]'
                . $verb . '[\'"]|[\'"]' . $verb
                . '[\'"]\s*(?:={2,3}|!={1,2})/';
            preg_match($comparisonPattern, $body)
                and $isDetected = true;

            // Patrón 2 — in_array con verbo como primer
            // argumento: in_array('GET', [...])
            $inArrayFirstArg = '/in_array\s*\(\s*[\'"]'
                . $verb . '[\'"]/';
            preg_match($inArrayFirstArg, $body)
                and $isDetected = true;

            // Patrón 3 — case 'VERBO': dentro de un switch —
            // no verificamos que el switch sea específicamente
            // sobre REQUEST_METHOD (requeriría parsing real),
            // pero 'case' seguido de un verbo HTTP literal es
            // suficientemente específico como señal confiable
            // en la práctica de este framework.
            $casePattern = '/case\s+[\'"]' . $verb . '[\'"]\s*:/';
            preg_match($casePattern, $body)
                and $isDetected = true;

            // Patrón 4 — el verbo aparece como elemento de un
            // array literal que es argumento de in_array (no
            // necesariamente el primero):
            // in_array($x, ['GET','POST',...]). Heurística
            // basada en cercanía, no parsing real de AST.
            $inArrayElementPattern = '/in_array\s*\([^)]*\['
                . '[^\]]*[\'"]' . $verb . '[\'"][^\]]*\][^)]*\)/';
            preg_match($inArrayElementPattern, $body)
                and $isDetected = true;

            $isDetected and $found[] = strtolower($verb);
        endforeach;

        return empty($found) ? ['get'] : $found;
    }

    private function _actionToRoute(
        string $controller, string $action
    ): string {
        return "/{$controller}/{$action}";
    }

    private function _loadOverrides(): array {
        if (!file_exists($this->_overridesPath)):
            return [];
        endif;
        return json_decode(
            file_get_contents($this->_overridesPath), true
        ) ?? [];
    }

    /**
     * Fusiona el esqueleto generado con los overrides
     * manuales. Los overrides SIEMPRE ganan sobre lo
     * generado — permite enriquecer cualquier campo
     * (summary, description, examples, responses
     * específicas) sin que la regeneración lo borre.
     */
    private function _merge(array $skeleton, array $overrides): array {
        return $this->_deepMergeWithReplacement(
            $skeleton, $overrides
        );
    }

    /**
     * Fusión recursiva, con una excepción: si un nodo
     * del override es un objeto "minimal" (solo trae
     * 'description', sin más estructura), reemplaza
     * por completo el nodo correspondiente del esqueleto
     * en vez de fusionarlo campo por campo — evita que
     * un "no soportado" quede contaminado con contenido
     * heredado (ej: un 200 OK genérico de la heurística).
     */
    private function _deepMergeWithReplacement(
        array $base, array $override
    ): array {
        foreach ($override as $key => $value):
            if ($this->_isMinimalReplacement($value)):
                $base[$key] = $value;
            elseif (is_array($value)
                && isset($base[$key])
                && is_array($base[$key])
                && !$this->_isList($value)):
                $base[$key] = $this->_deepMergeWithReplacement(
                    $base[$key], $value
                );
            else:
                $base[$key] = $value;
            endif;
        endforeach;

        return $base;
    }

    private function _isMinimalReplacement($value): bool {
        return is_array($value)
            && array_key_exists('description', $value)
            && count($value) === 1;
    }

    private function _isList(array $value): bool {
        return array_is_list($value);
    }
}

// Ejecución CLI
$controllersPath    = $argv[1] ?? 'app/controllers';
$overridesPath      = $argv[2] ?? 'app/openapi/overrides.json';
$outputPath         = $argv[3] ?? 'app/webroot/api.json';
$excludeControllers = isset($argv[4])
    ? explode(',', $argv[4])
    : [];

$generator = new Generator(
    $controllersPath, $overridesPath, $outputPath,
    $excludeControllers
);
$generator->generate();
