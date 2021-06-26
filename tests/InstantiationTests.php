<?php

declare(strict_types=1);

namespace WeasyPrint\Tests;

use WeasyPrint\{Contracts\Factory, Facade, Service};

class InstantiationTests extends TestCase
{
  /** @covers WeasyPrint\Service */
  public function testCanBeInstantiatedDirectly(): void
  {
    $this->runInstanceAssertion(Service::new());
  }

  /** @covers WeasyPrint\Factory */
  public function testCanBeInstantiatedViaDependencyInjection(): void
  {
    $this->runInstanceAssertion($this->app->make(Factory::class));
  }

  /** @covers WeasyPrint\Facade */
  public function testCanBeInstantiatedViaFacade(): void
  {
    $this->runInstanceAssertion(Facade::getFacadeRoot());
  }

  protected function runInstanceAssertion($service): void
  {
    $this->assertInstanceOf(Service::class, $service);
  }
}
