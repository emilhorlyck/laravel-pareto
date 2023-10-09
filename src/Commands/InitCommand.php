<?php

namespace EmilHorlyck\LaravelPareto\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class ParetoInitCommand extends Command
{
    public $signature = 'pareto:init';

    public $description = 'Start a new project with the base configuration';

    public function handle(): int
    {
        // - [x] Install blueprint
        // - [ ] Setup backup
        // - [ ] API Documentation
        // - [x] Generate ERD
        // - [ ] Readme
        // - [ ] Conventional commit script
        // - [ ] Release script
        // - [ ] Pre push git hook for pest
        // - [ ] Run tests
        // - [x] Fillament Admin panel

        $initSteps = [
            'blueprint' => 'Install blueprint',
            // 'backup' => 'Setup backup',
            'api-docs' => 'API Documentation',
            'erd' => 'Generate ERD',
            // 'readme' => 'Readme',
            // 'conventional-commits' => 'Conventional commit script',
            // 'release' => 'Release script',
            // 'pre-push' => 'Pre push git hook for pest',
            // 'tests' => 'Run tests',
            'admin' => 'Fillament Admin panel',
        ];

        $chosenSteps = collect(
            multiselect(
                label: 'What would you like to get started?',
                options: $initSteps,
                // default: $initSteps,
            )
        );

        // TODO: extract to action
        // Blueprint installation
        if ($chosenSteps->contains('blueprint')) {
            info('Installing blueprint...');
            exec('composer require --dev laravel-shift/blueprint');
            info('   - enabling test assertions...');
            exec('composer require --dev jasonmccreary/laravel-test-assertions');
            info('   - enabling pest tests...');
            exec('composer require --dev laravel-shift/blueprint fidum/laravel-blueprint-pestphp-addon');
            exec('echo "/draft.yaml" >> .gitignore');
            exec('echo "/.blueprint" >> .gitignore');
            info('blueprint installed successfully.');
        }

        // Blueprint definition
        if ($chosenSteps->contains('blueprint')) {

            info('Creating draft.yaml...');
            exec('touch draft.yaml');

            info('Please define your models in draft.yaml - look at the documentation for more information.');
            info('Link to documentation: https://blueprint.laravelshift.com/docs/generating-components/');

            $blueprintDocs = confirm(
                label: 'Should we open blueprint docs in chrome?',
            );

            if ($blueprintDocs) {
                exec('open -a "Google Chrome.app" https://blueprint.laravelshift.com/docs/generating-components/');
            }

            if (confirm(
                label: 'should we open the draft.yaml file in vscode?',
            )) {
                exec('code ./draft.yaml');
            }

            $confirmed = confirm(
                label: 'Have you defined your Blueprint setup?',
                required: 'You must provide a blueprint setup before continuing.'
            );

            exec('php artisan blueprint:build');
            info('blueprint definition created successfully.');

            if (confirm(
                label: 'do you want to a Migrate:fresh?')) {

                //Todo: ask if we should seed the database

                exec('php artisan migrate:fresh');
            }
        }

        // Backup installation
        if ($chosenSteps->contains('backup')) {
            $this->info('Installing backup...');
        }

        // API Documentation
        if ($chosenSteps->contains('api-docs')) {
            $this->info('Installing api-docs...');
            exec('composer require dedoc/scramble');
            exec('composer require doctrine/dbal');
            info('api docs installed successfully. and available at /docs/api and /docs/api.json');
            info('Docs are only visible in local environment.');
        }

        //Generate ERD
        if ($chosenSteps->contains('erd')) {
            $this->info('Generating ERD...');
            exec('composer require beyondcode/laravel-er-diagram-generator --dev
            ');
            exec('php artisan generate:erd generated_erd.png');

            exec('open generated_erd.png');

            info('ERD generated successfully.');
        }

        // Readme
        if ($chosenSteps->contains('readme')) {
            info('Creating readme.md...');
        }

        // Conventional commit script

        // Release script

        // Pre push git hook for pest

        // Fillament Admin panel
        if ($chosenSteps->contains('admin')) {
            $this->info('Installing Filament...');
            // exec('composer require signifly/fillament');
            // exec('php artisan fillament:install');

            exec('composer require filament/filament:"^3.0-stable" --update-with-all-dependencies');
            $this->info('Installing Filament panels...');
            exec('php artisan filament:install --panels -q -n');

            if (confirm(label: 'Do you want to create a user now?')) {

                $name = text('What is your name?');

                $email = text('What is your email?');

                $password = password('What is your password?');

                exec('php artisan make:filament-user --name="'.$name.'" --email="'.$email.'" --password="'.$password.'"');
            }

            $this->info('models: '.$this->getModelNames()->implode(', '));

            $adminModels = collect(
                multiselect(
                    label: 'What models would you like to add to the Admin interface?',
                    options: $this->getModelNames()->values()->toArray(),
                )
            );

            $adminModels->each(function ($model) {
                $this->info('Adding '.$model.' to the Admin interface...');
                exec('php artisan make:filament-resource '.$model.' --generate');
            });

            info('Fillament installed successfully. go to /admin to see the admin panel.');
        }

        // Run tests
        if ($chosenSteps->contains('tests')) {
            exec('./vendor/bin/pest --init');
            exec('./vendor/bin/pest');
        }

        return self::SUCCESS;
    }

    public function getModelNames(): Collection
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {

                $path = $item->getRelativePathName();
                $class = sprintf('\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\'));

                return $class;
            })
            ->filter(function ($class) {
                $valid = false;

                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) &&
                        ! $reflection->isAbstract();
                }

                return $valid;
            });

        $modelsNames = $models->map(function ($model) {

            $classNameComponents = explode('\\', $model);

            return end($classNameComponents);
        });

        return $modelsNames; //$models->values();
    }
}
