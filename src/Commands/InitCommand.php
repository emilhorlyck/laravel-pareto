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

class InitCommand extends Command
{
    public $signature = 'pareto:init';

    public $description = 'Start a new project with the base configuration';

    public function handle(): int
    {
        // - [x] Readme
        // - [x] Install blueprint
        // - [ ] Setup backup
        // - [x] API Documentation
        // - [x] Generate ERD
        // - [ ] Activity log for models
        // - [ ] Conventional commit script
        // - [ ] Release script
        // - [ ] Pre push git hook for pest
        // - [ ] Add tests
        // - [x] Fillament Admin panel

        $initSteps = [
            'readme' => 'Replace readme',
            'blueprint' => 'Install blueprint',
            // 'backup' => 'Setup backup',
            'api-docs' => 'API Documentation',
            'erd' => 'Generate ERD',
            'activity-log' => 'Activity log for models',
            // 'conventional-commits' => 'Conventional commit script',
            // 'release' => 'Release script',
            // 'pre-push' => 'Pre push git hook for pest',
            'tests' => 'Add tests',
            'admin' => 'Fillament Admin panel',
        ];

        $chosenSteps = collect(
            multiselect(
                label: 'What would you like to get started?',
                options: $initSteps,
                // default: $initSteps,
            )
        );

        if ($chosenSteps->isEmpty()) {
            info('You must choose at least one step to continue.');

            return self::FAILURE;

        } else {
            $this->addTooReadme([
                '## Packages installed using [Laravel-Pareto](https://github.com/emilhorlyck/laravel-pareto)',
            ]);
        }

        if ($chosenSteps->contains('readme')) {
            $this->info('Replacing readme...');
            exec('rm README.md');
            exec('touch README.md');

            $gitAuthor = text('what is you gitHub handle');
            $githubUrl = text('What is the repo url?');

            $this->addTooReadme([

                '![Logo](https://dev-to-uploads.s3.amazonaws.com/uploads/articles/th5xamgrr6se0x5ro4g6.png)',
                '',
                '',
                '# Project Title',
                '',
                'A brief description of what this project does and who it is used for',
                '',
                '',
                '## Tech Stack',
                '',
                '**TALL:** Tailwind, Alpine.js, Laravel, and Livewire',
                '',
                '## Demo',
                '',
                'Insert gif or link to demo',
                '',
                '',
                '## Run Locally',
                '',
                'Clone the project',
                '',
                '\`\`\`bash',
                'git clone '.$githubUrl,
                '\`\`\`',
                '',
                'Go to the project directory',
                '',
                '\`\`\`bash',
                'cd my-project',
                '\`\`\`',
                '',
                'Install dependencies',
                '',
                '\`\`\`bash',
                'composer install',
                '\`\`\`',
                '',
                'Start the server',
                '',
                '\`\`\`bash',
                'php artisan serve',
                '\`\`\`',
                '',
                '',
                '## Running Tests',
                '',
                'To run tests, run the following command',
                '',
                '\`\`\`bash',
                'php artisan vendor/bin/pest',
                '\`\`\`',
                '',
                '',
                '## Deployment',
                '',
                'To deploy this project push the code to github and make a PR against develop',
                '',
                '',
                '## Authors ',
                '',
                '- [@'.$gitAuthor.'](https://www.github.com/'.$gitAuthor.')',
                '',
                '## CodeOwner',
                '',
                '- [@'.$gitAuthor.'](https://www.github.com/'.$gitAuthor.')',
                '',
                '',
                '## Related',
                '',
                'Here are some related projects',
                '',
                '## Links',
                '',
                '',
                '## Maintenance',
                '',
                'Maintenance windows are agreed to be:',
                'No windows agreed.',
            ]);

            $this->info('Readme created succesfully');
        }

        // TODO: extract to action
        // Blueprint installation
        if ($chosenSteps->contains('blueprint')) {

            // $this->addTooReadme('echo "### [Blueprint](Blueprint) from function');

            info('Installing blueprint...');
            exec('composer require --dev laravel-shift/blueprint');
            info('   - enabling test assertions...');
            exec('composer require --dev jasonmccreary/laravel-test-assertions');
            info('   - enabling pest tests...');
            exec('composer require --dev laravel-shift/blueprint fidum/laravel-blueprint-pestphp-addon');
            exec('echo "/draft.yaml" >> .gitignore');
            exec('echo "/.blueprint" >> .gitignore');
            info('blueprint installed successfully.');

            // Blueprint definition
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

            $this->addTooReadme([
                '### [Blueprint](https://blueprint.laravelshift.com/docs/generating-components/)',
                '#### Usage',
                '',
                'Build the application from the draft.yaml file using the following command:',
                '\`\`\`bash',
                'php artisan blueprint:build',
                '\`\`\`',
                '',
                'Undo the last blueprint build using the following command:',
                '\`\`\`bash',
                'php artisan blueprint:erase',
                '\`\`\`',
                '',
            ]);
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

            $this->addTooReadme([
                '### [Generate ERD](exampl.com)',
                '#### Usage',
                '',
                'Generate an ERD',
                '\`\`\`bash',
                'php artisan generate:erd',
                '\`\`\`',
            ]);

            $this->addTooReadme([
                '## Generated ERD',
                '![ERD](generated_erd.png)',
                'To update the ERD in the Readme run the following command',
                '\`\`\`bash',
                'php artisan generate:erd generated_erd.png',
                '\`\`\`',
            ]);
        }

        // Activity log for models
        if($chosenSteps->contains('activity-log')) {
            $this->info('Installing activity log...');
            exec('composer require spatie/laravel-activitylog');
            info('activity log installed successfully.');
            exec('php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"');
            exec('php artisan migrate');
            info('activity log migrations migrated successfully.');

            $this->addTooReadme([
                '### [laravel-activitylog](https://spatie.be/docs/laravel-activitylog/v4/basic-usage/logging-activity)',
                '#### Usage',
                '',
                'Log an activity',
                '\`\`\`php',
                'activity()->log(\'Look, I logged something\');',
                '\`\`\`',

                'The package can automatically log events such as when a model is created, updated and deleted.',
                'To make this work all you need to do is let your model use the Spatie\Activitylog\Traits\LogsActivity-trait.',
                '',
                '[Logging model events](https://spatie.be/docs/laravel-activitylog/v4/advanced-usage/logging-model-events)',
                '',
            ]);
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

        // Add tests
        if ($chosenSteps->contains('tests')) {
            $this->info('Adding tests...');
            exec('php artisan vendor:publish --tag=Laravel-pareto-tests');
            $this->info('Tests added successfully.');
            exec('./vendor/bin/pest --init');
            // exec('./vendor/bin/pest');
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

    public function addTooReadme(array $lines)
    {
        foreach ($lines as $line) {
            exec('echo "'.$line.'" >> README.md');
        }
    }
}
