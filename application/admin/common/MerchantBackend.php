<?php

namespace app\admin\common;

use app\common\controller\Backend;
use think\Session;

/**
 * 商家关联共通后台基类
 */
class MerchantBackend extends Backend
{

    /**
     * 绑定商家ID搜索
     */
    protected $merchantWhere = null;

    public function _initialize()
    {
        parent::_initialize();

        if (session('?merchant')) {
            $this->merchantWhere = ['merchant_id', '=', session('merchant')['id']];
            $this->assign('merchant', session('merchant'));
            $this->assign('hotelType', session('merchant')['merchantpropertieshotel']['hotel_type']);
        }

    }

    /**
     * 生成查询所需要的条件,排序方式
     * @param mixed $searchfields 快速查询的字段
     * @param boolean $relationSearch 是否关联查询
     * @param string $ifNotExistsSearch 不存在则检索条件，目前只支持单字段
     * @return array
     */
    protected function buildparams($searchfields = null, $relationSearch = null, $ifNotExistsSearch = null)
    {
        $searchfields = is_null($searchfields) ? $this->searchFields : $searchfields;
        $relationSearch = is_null($relationSearch) ? $this->relationSearch : $relationSearch;
        $search = $this->request->get("search", '');
        $filter = $this->request->get("filter", '');
        $op = $this->request->get("op", '', 'trim');
        $sort = $this->request->get("sort", "id");
        $order = $this->request->get("order", "DESC");
        $offset = $this->request->get("offset", 0);
        $limit = $this->request->get("limit", 0);
        $filter = (array)json_decode($filter, true);
        $op = (array)json_decode($op, true);
        $filter = $filter ? $filter : [];
        $where = [];
        $tableName = '';
        if( !is_null($this->merchantWhere) ) {
            $where[] = $this->merchantWhere;
        }
        if ($relationSearch) {
            if (!empty($this->model)) {
                $name = \think\Loader::parseName(basename(str_replace('\\', '/', get_class($this->model))));
                $tableName = $name . '.';
            }
            $sortArr = explode(',', $sort);
            foreach ($sortArr as $index => & $item) {
                $item = stripos($item, ".") === false ? $tableName . trim($item) : $item;
            }
            unset($item);
            $sort = implode(',', $sortArr);
        }
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            $where[] = [$tableName . $this->dataLimitField, 'in', $adminIds];
        }
        if ($search) {
            $searcharr = is_array($searchfields) ? $searchfields : explode(',', $searchfields);
            foreach ($searcharr as $k => &$v) {
                $v = stripos($v, ".") === false ? $tableName . $v : $v;
            }
            unset($v);
            $where[] = [implode("|", $searcharr), "LIKE", "%{$search}%"];
        }
        foreach ($filter as $k => $v) {
            $sym = isset($op[$k]) ? $op[$k] : '=';
            if (stripos($k, ".") === false) {
                $k = $tableName . $k;
            }
            $v = !is_array($v) ? trim($v) : $v;
            $sym = strtoupper(isset($op[$k]) ? $op[$k] : $sym);
            switch ($sym) {
                case '=':
                case '<>':
                    $where[] = [$k, $sym, (string)$v];
                    break;
                case 'LIKE':
                case 'NOT LIKE':
                case 'LIKE %...%':
                case 'NOT LIKE %...%':
                    $where[] = [$k, trim(str_replace('%...%', '', $sym)), "%{$v}%"];
                    break;
                case '>':
                case '>=':
                case '<':
                case '<=':
                    $where[] = [$k, $sym, intval($v)];
                    break;
                case 'FINDIN':
                case 'FINDINSET':
                case 'FIND_IN_SET':
                    $where[] = "FIND_IN_SET('{$v}', " . ($relationSearch ? $k : '`' . str_replace('.', '`.`', $k) . '`') . ")";
                    break;
                case 'IN':
                case 'IN(...)':
                case 'NOT IN':
                case 'NOT IN(...)':
                    $where[] = [$k, str_replace('(...)', '', $sym), is_array($v) ? $v : explode(',', $v)];
                    break;
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'BETWEEN' ? '<=' : '>';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'BETWEEN' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = [$k, $sym, $arr];
                    break;
                case 'RANGE':
                case 'NOT RANGE':
                    $v = str_replace(' - ', ',', $v);
                    $arr = array_slice(explode(',', $v), 0, 2);
                    if (stripos($v, ',') === false || !array_filter($arr)) {
                        continue 2;
                    }
                    //当出现一边为空时改变操作符
                    if ($arr[0] === '') {
                        $sym = $sym == 'RANGE' ? '<=' : '>';
                        $arr = $arr[1];
                    } elseif ($arr[1] === '') {
                        $sym = $sym == 'RANGE' ? '>=' : '<';
                        $arr = $arr[0];
                    }
                    $where[] = [$k, str_replace('RANGE', 'BETWEEN', $sym) . ' time', $arr];
                    break;
                case 'LIKE':
                case 'LIKE %...%':
                    $where[] = [$k, 'LIKE', "%{$v}%"];
                    break;
                case 'NULL':
                case 'IS NULL':
                case 'NOT NULL':
                case 'IS NOT NULL':
                    $where[] = [$k, strtolower(str_replace('IS ', '', $sym))];
                    break;
                default:
                    break;
            }
        }
        if(!is_null($ifNotExistsSearch)) {
            $hasField = false;
            foreach($where as $sub) {
                if($sub[0]==$ifNotExistsSearch[0]) {
                    $hasField = true;
                }
            }
            if(!$hasField) {
                $where[] = $ifNotExistsSearch;
            }
        }
        $where = function ($query) use ($where) {
            foreach ($where as $k => $v) {
                if (is_array($v)) {
                    call_user_func_array([$query, 'where'], $v);
                } else {
                    $query->where($v);
                }
            }
        };
        return [$where, $sort, $order, $offset, $limit];
    }
}
