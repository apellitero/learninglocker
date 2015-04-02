<?php namespace Controllers\API;

use \Locker\Data\Analytics\AnalyticsInterface as AnalyticsData;
use \Locker\Repository\Query\QueryRepository as QueryRepository;
use \Locker\Helpers\Exceptions as Exceptions;

class Statements extends Base {
  protected $activity, $query;

  /**
   * Constructs a new StatementController.
   * @param Document $document
   * @param Activity $activity
   */
  public function __construct(AnalyticsData $analytics, QueryRepository $query) {
    parent::__construct();
    $this->analytics = $analytics;
    $this->query = $query;
  }

  /**
   * Filters statements using the where method.
   * @return [Statement]
   */
  public function where() {
    $limit = \LockerRequest::getParam('limit', 100);
    $filters = json_decode(
      \LockerRequest::getParam('filters'),
      true
    ) ?: [];
    return \Response::json($this->query->where($this->lrs->_id, $filters)->paginate($limit));
  }

  /**
   * Filters statements using the aggregate method.
   * @return Aggregate http://php.net/manual/en/mongocollection.aggregate.php#refsect1-mongocollection.aggregate-examples
   */
  public function aggregate() {
    $pipeline = json_decode(
      \LockerRequest::getParam('pipeline'),
      true
    ) ?: [['match' => []]];
    return \Response::json($this->query->aggregate($this->lrs->_id, $pipeline)); 
  }

  /**
   * Aggregates by time.
   * @return Aggregate http://php.net/manual/en/mongocollection.aggregate.php#refsect1-mongocollection.aggregate-examples
   */
  public function aggregateTime() {
    $match = json_decode(
      \LockerRequest::getParam('match'),
      true
    ) ?: [];
    return \Response::json($this->query->aggregateTime($this->lrs->_id, $match));
  }

  /**
   * Aggregates by object.
   * @return Aggregate http://php.net/manual/en/mongocollection.aggregate.php#refsect1-mongocollection.aggregate-examples
   */
  public function aggregateObject() {
    $match = json_decode(
      \LockerRequest::getParam('match'),
      true
    ) ?: [];
    return \Response::json($this->query->aggregateObject($this->lrs->_id, $match));
  }

  /**
   * Return raw statements based on filter
   * @param Object $options
   * @return Json $results
   **/
  public function index(){
    $section = json_decode(LockerRequest::getParam('sections', '[]'));

    $data = $this->analytics->analytics(
      $this->lrs->_id,
      LockerRequest::getParams(),
      'statements',
      $section
    );

    if ($data['success'] == false) {
      throw new Exceptions\Exception(trans('apps.no_data'));
    }

    return $this->returnJson($data['data']);
  }
}