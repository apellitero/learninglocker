<?php namespace Tests;
use \Locker\Helpers\Helpers as Helpers;

abstract class StatementsTestCase extends LrsTestCase {
  protected $statements;

  public function setUp() {
    parent::setUp();
    $this->statements = [$this->createStatement(
      $this->lrs,
      $this->generateStatement()
    )];
  }

  protected function generateStatement($statement = []) {
    $timestamp = Helpers::getCurrentDate();
    return array_merge([
      'id' => '00000000-0000-0000-0000-000000000000',
      'actor' => [
        'mbox' => 'mailto:test@example.com',
        'objectType' => 'Agent'
      ],
      'verb' => [
        'id' => 'http://www.example.com/verbs/test'
      ],
      'object' => [
        'id' => 'http://www.example.com/objects/test',
        'objectType' => 'Activity'
      ],
      'timestamp' => $timestamp,
      'stored' => $timestamp,
      'authority' => [
        'mbox' => 'mailto:test@example.com',
        'objectType' => 'Agent'
      ],
      'version' => '1.0.1'
    ], $statement);
  }

  protected function createStatement(\Lrs $lrs, array $statement) {
    $model = new \Statement([
      'lrs' => ['_id' => $lrs->_id],
      'statement' => $statement,
      'active' => true,
      'voided' => false,
      'refs' => []
    ]);
    $model->timestamp = new \MongoDate(strtotime($model->statement['timestamp']));
    $model->save();
    return $model;
  }

  public function tearDown() {
    array_map(function ($statement) {
      $statement->delete();
    }, $this->statements);
    parent::tearDown();
  }
}
