<?php
/**
 * Extension Will Paginate.
 *
 * Se encarga de la paginacion de los items en las vistas.
 * @version 1.0
 * @author Javier Serrano.
 * @package Core
 * @subpackage Extensions
 */
	/**
	 * Paginate($params[per_page, page, conditions, fields, group, sort, varPageName])
	 *
	 * Pagina resultados, embebiendo Find() con condiciones.
	 * @param array $params
	 * @param object $model el objeto de donde se est&acute; invocando la funcion.
	 * @throws Exception Si no se llama desde un objeto modelo ActiveRecord.
	 * @return ActiveRecord
	 */
	function Paginate($params = NULL, &$model = NULL){
		if($model === NULL):
			throw new Exception("Paginate must be called in instace of a model object.");
			return NULL;
		endif;
		if(is_array($params) && sizeof($params) === 1 && !empty($params[0])) $params = $params[0];
		$arr_params = array();
		$arr_2 = array();
		$per_page = (isset($params['per_page']))?$params['per_page']:10;
		$page_num = (isset($params['page']))?$params['page']:1;
		$start = ($page_num-1)*$per_page;
		if(isset($params['conditions'])) $arr_2['conditions'] = $arr_params['conditions'] = $params['conditions'];
		if(isset($params['fields'])) $arr_params['fields'] = $params['fields'];
		if(isset($params['group'])) $arr_2['group'] = $arr_params['group'] = $params['group'];
		if(isset($params['sort'])) $arr_2['sort'] = $arr_params['sort'] = $params['sort'];
		$arr_params['limit'] = $start.",".$per_page;
		$arr_2['fields'] = 'COUNT(id)';
		$obj = $model->Find($arr_params);
		$obj->PaginatePageVarName = !empty($params['varPageName'])? $params['varPageName'] : 'page';
		$obj->PaginateTotalItems = $model->Find($arr_2)->{'COUNT(id)'};
		$obj->PaginateTotalPages = ceil($obj->PaginateTotalItems/$per_page);
		$obj->PaginatePageNumber = $page_num;
		return clone($obj);
	}
	/**
	 * Construye el html para mostrar las paginas segun resultados.
	 * @param array $params
	 * @param object $page
	 * @throws Exception
	 * @return NULL|string
	 */
	function WillPaginate($params = NULL, &$object = NULL){
		if(is_array($params) && sizeof($params) === 1 && !empty($params[0])) $params = $params[0];
		$str = '';
		$tail = '';
		$i = 1;
		if($object->PaginatePageNumber > 1):
			$str .= '<a class="paginate paginate-first-page" href="?'.$object->PaginatePageVarName.'=1">|&lt;&lt;</a>&nbsp;';
			$str .= '<a class="paginate paginate-prev-page" href="?'.$object->PaginatePageVarName.'='.($object->PaginatePageNumber-1).'">&lt;</a>&nbsp;';
		endif;
		$top = $object->PaginateTotalPages;
		if($object->PaginateTotalPages > 10):
			$top = ($object->PaginatePageNumber-1)+10;
			if($top > $object->PaginateTotalPages) $top = $object->PaginateTotalPages;
			$i = $top-10;
			if($i < 1) $i = 1;
		endif;
		if($object->PaginatePageNumber < $object->PaginateTotalPages):
			$tail .= '<a class="paginate paginate-next-page" href="?'.$object->PaginatePageVarName.'='.($object->PaginatePageNumber+1).'">&gt;</a>&nbsp;';
			$tail .= '<a class="paginate paginate-last-page" href="?'.$object->PaginatePageVarName.'='.($object->PaginateTotalPages).'">&gt;&gt;|</a>&nbsp;';
		endif;
		for(; $i <= $top; $i++){
			$str .= '<a class="paginate paginate-page'.($object->PaginatePageNumber == $i ? " paginate-active-page" : "").'" href="?'.$object->PaginatePageVarName.'='.$i.'">'.$i.'</a>&nbsp;';
		}
		$str .= $tail;
		return $str;
	}
?>
