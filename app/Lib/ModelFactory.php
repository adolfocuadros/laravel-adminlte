<?php
namespace App\Lib;


use Illuminate\Http\Request;

class ModelFactory
{
    //Nombre del modelo en string
    protected $model;
    protected $request;

    protected $paginate = 15;
    protected $query = null;

    protected $searchCols = [];
    protected $sortables = [];
    protected $relations = [];
    protected $filterCols = [];

    protected $defaultSort = [
        'id', 'desc'
    ];

    protected $methodSearch = null;
    protected $methodFilter = null;

    protected $queryGenerate = false;

    //Parameters
    protected $params = [
        'search' => 'filter',
        'perPage' => 'per_page',
        'sortBy' => 'sort',
        'filter' => 'fil',
        'withRelation' => 'withRelation'
    ];

    public function __construct($model)
    {
        $this->model = $model;
        $this->request = request();
        if(is_string($model)) {
            $this->query = $model::query();
        } else {
            $this->query = $model;
        }

    }

    public function availableSortables($sortables)
    {
        if(is_array($sortables)) {
            $this->sortables = $sortables;
        } else {
            $this->sortables = func_get_args();
        }
    }

    public function availableRelations($relations)
    {
        if(is_array($relations)) {
            $this->relations = $relations;
        } else {
            $this->relations = func_get_args();
        }
    }

    public function availableFilterCols($filterCols)
    {
        if(is_array($filterCols)) {
            $this->filterCols = $filterCols;
        } else {
            $this->filterCols = func_get_args();
        }
    }

    public function setPaginate(int $paginate)
    {
        $this->paginate = $paginate;
    }

    public function setDefaultSort($key, $order)
    {
        $this->defaultSort = [
            $key, $order
        ];
    }

    public function setSearchCols($searchCols)
    {
        if(is_array($searchCols)) {
            $this->searchCols = $searchCols;
        } else {
            $this->searchCols = func_get_args();
        }
    }

    public function setMethodSearch(callable $func)
    {
        $this->methodSearch = $func;
    }

    public function setMethodFilter(callable $func)
    {
        $this->methodFilter = $func;
    }

    /**
     * Generando Query de Consulta
     * */
    public function generateApiQuery()
    {
        $request = $this->request;

        //Aplicando ordenación
        if(!empty($request->get($this->params['sortBy'])) && !empty($this->sortables)) {
            list($sortCol, $sortDir) = explode('|', $request->get($this->params['sortBy']));
            if(in_array($sortCol, $this->sortables)) {
                $this->query->orderBy($sortCol, $sortDir);
            } else {
                $this->printError('No es posible ordernar por el parametro: '.$sortCol.', contacte al administrador.');
            }
        } else {
            $this->query->orderBy($this->defaultSort[0], $this->defaultSort[1]);
        }

        //Obteniendo Paginador
        $this->paginate = $request->has($this->params['perPage']) ? (int) $request->get($this->params['perPage']) : $this->paginate;

        //Aplicando Busqueda
        if($request->has($this->params['search']) && !empty($this->searchCols)) {
            //dd('aplica busqueda');
            $this->query->where(function($q) use($request) {
                //Filtrar por:
                $value = "%{$request->get($this->params['search'])}%";

                foreach($this->searchCols as $searchCol) {
                    //dd($searchCol);
                    $methodSearch = $this->methodSearch;
                    if($methodSearch != null && is_callable($methodSearch)) {
                        $methodSearch($q, $this->params['search']);
                    } else {
                        $q->orWhere($searchCol, 'like', $value);
                    }
                }
            });
        }

        //Aplicando Filtros
        if($request->has($this->params['filter']) && !empty($this->filterCols)) {
            //Descomponiendo en numero de filtros
            $filters = explode('|', $request->get($this->params['filter']));
            foreach ($filters as $filter) {
                list($filterCol, $filterVal) = explode(',', $filter);
                if(in_array($filterCol, $this->filterCols)) {
                    $methodFilter = $this->methodFilter;
                    if($methodFilter != null && is_callable($methodFilter)) {
                        $methodFilter($this->query, $filterVal);
                    } else {
                        $this->query->where($filterCol, $filterVal);
                    }
                } else {
                    $this->printError('No es posible filtrar por el parametro: '.$filterCol.', contacte al administrador.');
                }
            }
        }

        if($request->has($this->params['withRelation']) && !empty($this->filterCols)) {
            $relations = explode('|', $this->params['withRelation']);
            $relationsList = [];
            foreach ($relations as $relation) {
                if(in_array($relation, $this->relations)) {
                    $relationsList[] = $relation;
                } else {
                    $this->printError('Esta relacion no está disponible: '.$relation.', contacte al administrador.');
                }
            }
            if(!empty($relationsList)) {
                $this->query->with($relationsList);
            }

        }

        $this->queryGenerate = true;


        return $this->query;
    }

    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Obtiene la lista de resultados
     */
    public function paginate($paginate = null)
    {
        if(!$this->queryGenerate) $this->generateApiQuery();
        $paginate = !empty($paginate) ? $paginate : $this->paginate;
        return $this->query->paginate($paginate);
    }

    /**
     * Obtiene todos los resultados sin paginar
     */
    public function get()
    {
        if(!$this->queryGenerate) $this->generateApiQuery();
        return $this->query->get();
    }

    public function printError($message)
    {
        abort(400, $message);
    }
}