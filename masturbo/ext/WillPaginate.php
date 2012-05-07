<?php
/*
 * Plugin Will Paginate.
 * 
 * Se encarga de la paginacion de los items en las vistas.
 */

	/*
	* Paginate($params[per_page, page, conditions, fields, group, sort])
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
		$obj->PaginateTotalItems = $model->Find($arr_2)->{'COUNT(id)'};
		$obj->PaginateTotalPages = ceil($obj->PaginateTotalItems/$per_page);
		$obj->PaginatePageNumber = $page_num;
		return clone($obj);
	}
	
	function WillPaginate($params = NULL, &$page = NULL){
//		if($page === NULL):
//			throw new Exception("WillPaginate must be called in instace of a page object.");
//			return NULL;
//		endif;
		if(isset($params[0]) and is_array($params)) $params = $params[0];
		if(!is_object($params) or get_parent_class($params) != 'ActiveRecord'):
			throw new Exception("WillPaginate param must be a model object.");
			return NULL;
		endif;
		$str = '';
		$tail = '';
		$i = 1;
		if($params->PaginatePageNumber > 1):
			$str .= '<a href="?page=1">|&lt;&lt;</a>&nbsp;';
			$str .= '<a href="?page='.($params->PaginatePageNumber-1).'">&lt;</a>&nbsp;';
		endif;
		$top = $params->PaginateTotalPages;
		if($params->PaginateTotalPages > 10):
			$top = ($params->PaginatePageNumber-1)+10;
			if($top > $params->PaginateTotalPages) $top = $params->PaginateTotalPages;
			$i = $top-10;
			if($i < 1) $i = 1;
			//$tail = '...<a href="?page='.$params->PaginateTotalPages.'">'.$params->PaginateTotalPages.'</a>&nbsp;';
		endif;
		if($params->PaginatePageNumber < $params->PaginateTotalPages):
			$tail .= '<a href="?page='.($params->PaginatePageNumber+1).'">&gt;</a>&nbsp;';
			$tail .= '<a href="?page='.($params->PaginateTotalPages).'">&gt;&gt;|</a>&nbsp;';
		endif;
		for(; $i <= $top; $i++){
			$str .= '<a href="?page='.$i.'">'.$i.'</a>&nbsp;';
		}
		$str .= $tail;
		return $str;
	}
?>
