<?php

namespace Foundry\Core\Console\Commands;

use Foundry\Core\Console\Commands\Traits\MakesFilesFromStubs;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    use MakesFilesFromStubs;

	/**
	 * The console command signature.
	 *
	 * @var string
	 */
	protected $signature = 'foundry:make:module 
	{name : The name of the module in lower case as provider_name/module_name}
	{--feature= : The name of the feature to create. This would typically be the model name.}
	{--type= : The type of file to generate. Black to create all, or Model, Repository, Service, Inputs, Controller, Contract, Events, Permissions, Requests, Routes, UnitTest.}
	{--input=* : The inputs to create.}
	{--required=* : The required inputs.}
	{--fillable=* : The fillable inputs.}
	';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Creates a module folder with the basics.";

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
	    $base = $this->argument('name');

	    $_base = explode('/', $base);
	    if (count($_base) !== 2) {
	        throw new \Exception('Module name must be provided as "[provider]/[name]".');
        }

	    $provider = $_base[0];
        $name = $_base[1];

        $base_dir = base_path('modules');
        $module_dir = $base_dir . DIRECTORY_SEPARATOR . $provider . DIRECTORY_SEPARATOR . $name;
        $module_namespace = "Modules\\" . ucfirst(Str::camel($provider)) . "\\" . ucfirst(Str::camel($name));

        $type = $this->option('type');

        if ($feature = $this->option('feature')) {

            //check the module does not already exist
            if (!file_exists($module_dir)) {
                throw new \Exception('Module directory does not exists "'.$module_dir.'". Did you run this command without the feature option?');
            }

            //create the contract
            if (!$type || $type === 'Contract') $this->makeContract($feature, $base, $module_namespace, $module_dir);

            //create the model
            if (!$type || $type === 'Model') $this->makeModel($feature, $provider, $module_namespace, $module_dir);

            //create the repository
            if (!$type || $type === 'Repository') $this->makeRepository($feature, Str::snake($provider), $module_namespace, $module_dir);

            //create the service
            if (!$type || $type === 'Service') $this->makeService($feature, $module_namespace, $module_dir);

            //create the controller
            if (!$type || $type === 'Controller') $this->makeController($feature, $module_namespace, $module_dir);

            //create the permissions
            if (!$type || $type === 'Permissions') $this->makePermissions($feature, $name, $provider, $module_namespace, $module_dir);

            //create the resources
            if (!$type || $type === 'Resource') $this->makeResource($feature, $module_namespace, $module_dir);

            //events
            if (!$type || $type === 'Events') $this->makeEvents($feature, $module_namespace, $module_dir);

            //create the inputs
            if (!$type || $type === 'Inputs') $this->makeInputs($feature, $module_namespace, $module_dir);

            //create the requests
            if (!$type || $type === 'Requests') $this->makeRequests($feature, $provider, $module_namespace, $module_dir);

            //create the routes (append)
            if (!$type || $type === 'Route') $this->makeFeatureRoute($feature, $name, $provider, $module_namespace, $module_dir);

            //unit test
            if (!$type || $type === 'UnitTest') $this->makeUnitTest($feature, $name, $provider, $module_namespace, $module_dir);

        } else {

            //make the composer file
            if (!$type || $type === 'Composer') $this->makeComposer($base, $provider, $name, $module_dir);

            //create the service provider, events, and routes
            if (!$type || $type === 'Providers') $this->makeServiceProviders($name, $provider, $module_namespace, $module_dir);

            //create the base routes file
            if (!$type || $type === 'Route') $this->makeRoute($name, $provider, $module_namespace, $module_dir);
        }

    }

    /**
     * Builds the list of inputs to create
     *
     * @return Collection
     * @throws \Exception
     */
    public function getInputs()
    {
        $inputs = $this->option('input');
        $fillable = $this->option('fillable');
        $required = $this->option('required');

        $_inputs = new Collection();

        foreach ($inputs as $input) {
            $_input = explode(':', $input);

            $label = Arr::get($_input, 0);
            $name = Str::snake($label);
            $type = Arr::get($_input, 1);

            if (empty($_input)) {
                throw new \Exception(sprintf('Inputs incorrectly provided. Check input options are correct.'));
            }
            if (empty($type)) {
                throw new \Exception(sprintf('Input "%s" missing type', $label));
            }

            $__input = [
                'name' => $name,
                'type' => $type,
                'label' => $label,
                'required' => false,
                'fillable' => false,
                'cast' => null
            ];

            if (in_array($label, $required)) {
                $__input['required'] = true;
            }

            if (in_array($label, $fillable)) {
                $__input['fillable'] = true;
            }

            switch($type){
                case 'JsonInputType':
                    $__input['cast'] = 'array';
                    break;
                case 'DateInputType':
                    $__input['cast'] = 'datetime:Y-m-d';
                    break;
                case 'DateTimeInputType':
                    $__input['cast'] = 'datetime:Y-m-d\TH:i:sP';
                    break;
            }

            $_inputs->offsetSet($name, $__input);
        }
        return $_inputs;
    }


    /**
     * Write the composer file
     *
     * @param string $base
     * @param string $provider
     * @param string $name
     * @param string $module_dir
     */
    public function makeComposer(string $base, string $provider, string $name, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'composer.stub';
        $stub_variables = [
            'MODULE_NAME' => $base,
            'MODULE_PSR_NAMESPACE' => "Modules\\\\" . ucfirst(Str::camel($provider)) . "\\\\" . ucfirst(Str::camel($name)) . "\\\\"
        ];
        $this->writeStub('composer.json', $module_dir, $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the contract file
     *
     * @param string $feature
     * @param string $base
     * @param string $module_namespace
     * @param string $module_dir
     */
    public function makeContract(string $feature, string $base, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_contract.stub';
        $stub_variables = [
            'CONTRACT_NAME' => 'Is' . $feature,
            'CONTRACT_NAMESPACE' => $module_namespace . "\\" . "Entities" . "\\" . "Contracts"
        ];
        $this->writeStub('Is' . $feature . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Entities' . DIRECTORY_SEPARATOR . 'Contracts', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the contract file
     *
     * @param string $feature
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeModel(string $feature, string $provider, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_model.stub';

        $inputs = $this->getInputs();

        $fillable = $inputs->filter(function($value, $key){
            return $value['fillable'] === true;
        })->pluck('name')->toArray();

        $visible = $inputs->pluck('name')->toArray();
        $casts = $inputs->filter(function($value, $key){
            return !empty($value['cast']);
        })->pluck('cast')->toArray();

        $stub_variables = [
            'MODEL_NAME' => $feature,
            'MODEL_NAMESPACE' => $module_namespace . "\\" . "Models",
            'MODEL_TABLE_NAME' => Str::snake($provider) . '_' . Str::plural(Str::snake($feature)),
            'MODEL_CONTRACT_NAMESPACE' => $module_namespace . "\\" . "Entities" . "\\" . "Contracts",
            'MODEL_CONTRACT_NAME' => 'Is' . $feature,
            'FILLABLE' => $this->fieldNameArray($fillable),
            'VISIBLE' => $this->fieldNameArray($visible),
            'CASTS' => implode(",\r\n\t\t", $casts)
        ];
        $this->writeStub($feature . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the repository file
     *
     * @param string $feature
     * @param string $table_prefix
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeRepository(string $feature, string $table_prefix, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_repository.stub';

        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'REPOSITORY_NAME' => $feature . "Repository",
            'REPOSITORY_NAMESPACE' => $module_namespace . "\\" . "Repositories",
            'MODEL_TABLE_NAME' => $table_prefix . '_' . Str::plural(Str::snake($feature)),
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_OBJECT_NAME' => '$' . Str::snake($feature)
        ];
        $this->writeStub($feature . "Repository" . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Repositories', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the service file
     *
     * @param string $feature
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeService(string $feature, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_service.stub';

        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'SERVICE_NAME' => $feature . "Service",
            'SERVICE_NAMESPACE' => $module_namespace . "\\" . "Services",
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_OBJECT_NAME' => '$' . Str::snake($feature)
        ];
        $this->writeStub($feature . "Service" . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Repositories', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the controller file
     *
     * @param string $feature
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeController(string $feature, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_controller.stub';

        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::plural($feature)
        ];
        $this->writeStub(Str::plural($feature) . "Controller" . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Controllers', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the permissions file
     *
     * @param string $feature
     * @param string $name
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makePermissions(string $feature, string $name, string $provider, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_permissions.stub';

        $stub_variables = [
            'PROVIDER' => $provider,
            'NAME' => $name,
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'FEATURE_NAME_LOWERCASE' => Str::snake($feature)
        ];
        $this->writeStub("Sync" . $feature . "Permissions" . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Listeners', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the resource file
     *
     * @param string $feature
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeResource(string $feature, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_resource.stub';

        $inputs = $this->getInputs();

        $visible = $inputs->pluck('name')->toArray();
        $fields = implode(",\r\n\t\t\t", array_map(function ($name) {
            return "'" . $name . "' => \$this->$name";
        }, $visible));

        if ($fields) {
            $fields = $fields . ',';
        }

        $stub_variables = [
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_NAME' => $feature,
            'FEATURE_FIELDS' => $fields
        ];
        $this->writeStub($feature . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Resources', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the permissions file
     *
     * @param string $feature
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeEvents(string $feature, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_event_saved.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_OBJECT_NAME' => Str::snake($feature)
        ];
        $this->writeStub($feature . "Saved" . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Events', $this->makeStub($stub_variables, $stub_file));

        $events = [
            'Created',
            'Updated',
            'Deleted',
            'Restored'
        ];

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_event.stub';

        foreach ($events as $event) {
            $stub_variables['EVENT_NAME'] = $event;
            $this->writeStub($feature . $event . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Events', $this->makeStub($stub_variables, $stub_file));
        }

    }

    /**
     * Write the inputs file
     *
     * @param string $feature
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeInputs(string $feature, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_input.stub';

        $inputs = $this->getInputs();

        $FEATURE_INPUT_TYPES_USE = [];
        $FEATURE_INPUT_TYPES = [];
        $FEATURE_INPUT_TYPE_INPUTS = [];

        foreach ($inputs as $input) {
            $input_class_name = Str::ucfirst(Str::camel($input['name']));

            $FEATURE_INPUT_TYPES_USE[] = "use " . $module_namespace . "\\Inputs\\" . $feature . "\Types\\" . $input_class_name . ";";
            $FEATURE_INPUT_TYPES[] = $input_class_name . "::input()";
            $FEATURE_INPUT_TYPE_INPUTS[] = "RowType::withChildren(\$form->get('".$input['name']."'))";

            $stub_variables = [
                'INPUT_CLASS_NAME' => $input_class_name,
                'FEATURE_NAME' => $feature,
                'MODULE_NAMESPACE' => $module_namespace,
                'INPUT_NAME' => $input['name'],
                'INPUT_TYPE' => $input['type'],
                'INPUT_LABEL' => $input['label'],
                'INPUT_REQUIRED' => $input['required'] ? 'true' : 'false',
                'INPUT_ADDITIONAL' => null
            ];
            $this->writeStub($input_class_name . '.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Inputs' . DIRECTORY_SEPARATOR . $feature . DIRECTORY_SEPARATOR . 'Types', $this->makeStub($stub_variables, $stub_file));
        }

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_inputs_base.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_INPUT_TYPES_USE' => implode("\r\n", $FEATURE_INPUT_TYPES_USE),
            'FEATURE_INPUT_TYPES' => implode(",\r\n\t\t\t", $FEATURE_INPUT_TYPES),
            'FEATURE_INPUT_TYPE_INPUTS' => implode(",\r\n\t\t\t", $FEATURE_INPUT_TYPE_INPUTS)
        ];
        $this->writeStub($feature . 'Input.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Inputs' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_inputs_search.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace
        ];
        $this->writeStub("Search" . $feature . 'Input.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Inputs' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_inputs_action.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_ACTION' => 'Add'
        ];
        $this->writeStub("Add" . $feature . 'Input.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Inputs' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_ACTION' => 'Edit'
        ];
        $this->writeStub("Edit" . $feature . 'Input.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Inputs' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

    }

    /**
     * Write the permissions file
     *
     * @param string $feature
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeRequests(string $feature, string $provider, string $module_namespace, string $module_dir): void
    {
        //base
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_request_base.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace
        ];
        $this->writeStub($feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        //browse
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_request_browse.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider
        ];
        $this->writeStub('Browse' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        //read
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_request_entity.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider,
            'REQUEST_ACTION' => 'Read',
            'REQUEST_ACTION_LOWER' => 'read'
        ];
        $this->writeStub('Read' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));
        //edit
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider,
            'REQUEST_ACTION' => 'Edit',
            'REQUEST_ACTION_LOWER' => 'edit'
        ];
        $this->writeStub('Edit' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        //add
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_request_add.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider,
            'ACTION' => 'Add',
            'ACTION_LOWER' => 'add'
        ];
        $this->writeStub('Add' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));
        //select
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider,
            'ACTION' => 'Select',
            'ACTION_LOWER' => 'select'
        ];
        $this->writeStub('Select' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

        //delete
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_request_delete.stub';
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider,
            'REQUEST_ACTION' => 'Delete',
            'REQUEST_ACTION_LOWER' => 'delete'
        ];
        $this->writeStub('Delete' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));
        //restore
        $stub_variables = [
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'FEATURE_PLURAL_NAME' => Str::snake(Str::plural($feature)),
            'PROVIDER' => $provider,
            'REQUEST_ACTION' => 'Restore',
            'REQUEST_ACTION_LOWER' => 'delete'
        ];
        $this->writeStub('Restore' . $feature . 'Request.php', $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Http' . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $feature, $this->makeStub($stub_variables, $stub_file));

    }

    /**
     * Write the service providers
     *
     * @param string $name
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeServiceProviders(string $name, string $provider, string $module_namespace, string $module_dir): void
    {
        $stub_variables = [
            'MODULE_NAMESPACE' => $module_namespace,
            'NAME' => $name,
            'PROVIDER' => $provider
        ];

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_module_service_provider.stub';
        $this->writeStub("ModuleServiceProvider.php", $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Providers', $this->makeStub($stub_variables, $stub_file));

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_event_service_provider.stub';
        $this->writeStub("EventServiceProvider.php", $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Providers', $this->makeStub($stub_variables, $stub_file));

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_routes_service_provider.stub';
        $this->writeStub("RouteServiceProvider.php", $module_dir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Providers', $this->makeStub($stub_variables, $stub_file));

    }

    /**
     * Write the base route file
     *
     * @param string $name
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeRoute(string $name, string $provider, string $module_namespace, string $module_dir): void
    {
        $stub_variables = [
            'MODULE_NAMESPACE' => $module_namespace,
            'NAME' => $name,
            'PROVIDER' => $provider
        ];

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_route.stub';
        $this->writeStub("api.php", $module_dir . DIRECTORY_SEPARATOR . 'routes', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the base route file
     *
     * @param string $feature
     * @param string $name
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeFeatureRoute(string $feature, string $name, string $provider, string $module_namespace, string $module_dir): void
    {
        $stub_variables = [
            'MODULE_NAMESPACE' => $module_namespace,
            'NAME' => $name,
            'PROVIDER' => $provider,
            'FEATURE_NAME_PLURAL_LOWER' => Str::snake(Str::plural($feature)),
            'FEATURE_NAME_PLURAL' => Str::plural($feature)
        ];

        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_route_api.stub';
        $this->appendStub("api.php", $module_dir . DIRECTORY_SEPARATOR . 'routes', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * Write the permissions file
     *
     * @param string $feature
     * @param string $name
     * @param string $provider
     * @param string $module_namespace
     * @param string $module_dir
     * @throws \Exception
     */
    public function makeUnitTest(string $feature, string $name, string $provider, string $module_namespace, string $module_dir): void
    {
        $stub_file = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'feature_unit_test.stub';

        $stub_variables = [
            'PROVIDER' => $provider,
            'NAME' => $name,
            'FEATURE_NAME' => $feature,
            'MODULE_NAMESPACE' => $module_namespace,
            'NAME_PLURAL' => Str::snake(Str::plural($feature)),
            'MODEL_TABLE_NAME' => Str::snake($provider) . '_' . Str::plural(Str::snake($feature)),
        ];
        $this->writeStub($feature . "Test" . '.php', $module_dir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'Feature', $this->makeStub($stub_variables, $stub_file));
    }

    /**
     * @param array $names
     * @return string
     */
    public function fieldNameArray(array $names): string
    {
        return implode(",\r\n\t\t", array_map(function ($name) {
            return "'" . $name . "'";
        }, $names));
    }


}
