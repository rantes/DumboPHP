<?php
/*
 * Plugin Will Paginate.
 *
 * Se encarga de la paginacion de los items en las vistas.
 */

	/*
	* Paginate($params[per_page, page, conditions, fields, group, sort, varPageName])
	*
	*/
	function Paginate($params = NULL, &$model = NULL){
		if($model === NULL):
			throw new Exception("Paginate must be called in instace of a model object.");
			return NULL;
		endif;
		$params = $params[0];
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

	function WillPaginate($params = NULL, &$model = NULL){
		$object = null;
		if(!empty($model) and is_object($model)):
			$object = $model;
			$params = $params[0];
		elseif(is_array($params)):
			if(isset($params[0])):
				$object = $params[0];
			elseif(isset($params['object'])):
				$object = $params['object'];
			endif;
		endif;

		if(!is_object($object) or get_parent_class($object) != 'ActiveRecord'):
			throw new Exception("WillPaginate param must be a model object.");
			return NULL;
		endif;
		if(empty($object->PaginateClass)) $object->PaginateClass = ' class="paginate-links"';
		if(!empty($params['class'])) $object->PaginateClass = ' class="'.$params['class'].'"';
		if(empty($object->PaginateIdPrefix)) $object->PaginateIdPrefix = 'paginate-link';
		if(!empty($params['id'])) $object->PaginateIdPrefix = $params['id'];
		$str = '';
		$tail = '';
		$i = 1;
		if($object->PaginatePageNumber > 1):
			$str .= '<a href="?'.$object->PaginatePageVarName.'=1"'.$object->PaginateClass.' id="'.$object->PaginateIdPrefix.'-first">|&lt;&lt;</a>&nbsp;';
			$str .= '<a href="?'.$object->PaginatePageVarName.'='.($object->PaginatePageNumber-1).'"'.$object->PaginateClass.' id="'.$object->PaginateIdPrefix.'-prev">&lt;</a>&nbsp;';
		endif;
		$top = $object->PaginateTotalPages;
		if($object->PaginateTotalPages > 10):
			$top = ($object->PaginatePageNumber-1)+10;
			if($top > $object->PaginateTotalPages) $top = $object->PaginateTotalPages;
			$i = $top-10;
			if($i < 1) $i = 1;
		endif;
		if($object->PaginatePageNumber < $object->PaginateTotalPages):
			$tail .= '<a href="?'.$object->PaginatePageVarName.'='.($object->PaginatePageNumber+1).'"'.$object->PaginateClass.' id="'.$object->PaginateIdPrefix.'-next">&gt;</a>&nbsp;';
			$tail .= '<a href="?'.$object->PaginatePageVarName.'='.($object->PaginateTotalPages).'"'.$object->PaginateClass.' id="'.$object->PaginateIdPrefix.'-last">&gt;&gt;|</a>&nbsp;';
		endif;
		for(; $i <= $top; $i++):
			$str .= '<a href="?'.$object->PaginatePageVarName.'='.$i.'"'.$object->PaginateClass.' id="'.$object->PaginateIdPrefix.'-'.$i.'">'.$i.'</a>&nbsp;';
		endfor;
		$str .= $tail;
		return $str;
	}
?>
