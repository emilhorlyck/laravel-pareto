<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

test('app')
    ->expect('App\Actions')
    ->toBeInvokable();

test('app')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model');

test('app')
    ->expect('App\Jobs')
    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');

// Suffixes

test('app')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

test('app')
    ->expect('App\Jobs')
    ->toHaveSuffix('Job');

test('app')
    ->expect('App\Http\Clients')
    ->toHaveSuffix('Client');

test('app')
    ->expect('App\databases\factories')
    ->toHaveSuffix('Factory');

test('app')
    ->expect('App\database\seeders')
    ->toHaveSuffix('Seeder');

test('app')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

test('app')
    ->expect('App\Providers')
    ->toHaveSuffix('Provider');
