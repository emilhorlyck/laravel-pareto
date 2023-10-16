<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

test('invokable actions')
    ->expect('App\Actions')
    ->toBeInvokable();

test('models extend eloquent') 
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model');

test('jobs implement should queue')
    ->expect('App\Jobs')
    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');

// Suffixes

test('controllers end with controller')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

test('jobs end with job')
    ->expect('App\Jobs')
    ->toHaveSuffix('Job');

test('clients end with client')
    ->expect('App\Http\Clients')
    ->toHaveSuffix('Client');

test('factories end with factory')
    ->expect('App\databases\factories')
    ->toHaveSuffix('Factory');

test('seeders end with seeder')
    ->expect('App\database\seeders')
    ->toHaveSuffix('Seeder');

test('providers end with provider')
    ->expect('App\Providers')
    ->toHaveSuffix('Provider');
