<?php


namespace App\Modifiers;


use Illuminate\Support\Collection;

abstract class Modifier
{
  /* @var Collection */
  protected $collection;

  /* @var array */
  protected $steps;

  /**
   * Static initializer
   *
   * @param ...$var
  */
  public static function make(...$var): Modifier
  {
    return (new static(...$var));
  }

  /**
   * Creates a new instance
   *
   * @param Collection $collection
  */
  public function __construct(Collection $collection)
  {
    $this->collection = $collection;
    $this->steps = [];
  }

  /**
   * Function to add step
   *
   * @param string $name
   * @param ?array $params
   * @param bool $unique
   *
   * @return $this
  */
  protected function addStep(string $name, ?array $params = null, bool $unique = true): self {
    if (!$unique || !$this->findStep($name)) {
      array_push($this->steps, ['name' => $name, 'params' => $params ?? []]);
    }
    return $this;
  }

  /**
   * Function to find the step
   *
   * @param string $name
   *
   * @return ?array
  */
  protected function findStep(string $name): ?array {
    return array_filter(
      $this->steps,
      function ($s) use ($name) { return $s['name'] === $name; }
    )[0] ?? null;
  }

  /**
   * Executes the chain
   *
   * @return Collection
  */
  public function execute(): Collection {
    return $this->applyAll($this->collection, $this->steps);
  }

  /**
   * Method for applying the step
   *
   * @param Collection $data
   * @param string $name
   * @param array $params
   *
   * @return Collection
  */
  protected abstract function apply(Collection $data, string $name, array $params): Collection;

  /**
   * Method for using all steps
   *
   * @param Collection $data
   * @param array $steps
   *
   * @return Collection
  */
  protected function applyAll(Collection $data, array $steps): Collection {
    $result = $data;
    foreach ($steps as $step) {
      $result = $this->apply($result, $step['name'], $step['params']);
    }
    return $result;
  }
}