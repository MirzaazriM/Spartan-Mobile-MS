<?php
namespace Model\Service\Facade;

use Model\LockedState\LockedState;
use Model\Service\Facade\DataTypes\TagsType;
use Model\Service\Facade\DataTypes\WorkoutPlansType;
use Model\Service\Facade\DataTypes\NutritionsPlanType;
use Model\Service\Facade\DataTypes\RecepiesType;
use Model\Service\Facade\DataTypes\WorkoutsType;
use Model\Service\Facade\DataTypes\ExercisesType;
use Model\Service\Facade\DataTypes\PackagesType;

class DataFacade
{
    
    // Variables
    private $rawData;
    public $version;
    public $type;
    
    // Classes
    public $tagsType;
    public $workoutsPlanType;
    public $nutritionsPlanType;
    public $recepiesType;
    public $workoutsType;
    public $exercisesType;
    public $packageType;

    // locking variable
    public $lockingState;
    
    public function __construct($rawData, Int $version, string $type)
    {
        // Variables
        $this->rawData = $rawData;
        $this->version = $version;
        $this->type = $type;

        // Helper Classes
        $this->tagsType = new TagsType($this);
        $this->workoutsPlanType = new WorkoutPlansType($this);
        $this->nutritionsPlanType = new NutritionsPlanType($this);
        $this->recepiesType = new RecepiesType($this);
        $this->workoutsType = new WorkoutsType($this);
        $this->exercisesType = new ExercisesType($this);
        $this->packageType = new PackagesType($this);  //$this->packageType = new PackagesType($this, $version);

        // new locking/unlocking mechanism
        $this->lockingState = new LockedState($this->type);
    }
    
    
    /**
     * Handle Data
     * @return array
     */
    public function handle(): array
    {
        $this->packages();
        // consturct locked state packages
        $this->lockingState->constructPackages($this->packageType->getDataTemp()[0]);

        $this->workoutPlans();
        // consturct locked state workout plans
        $this->lockingState->constructWorkoutPlans($this->workoutsPlanType->getDataTemp()[0]);

        $this->nutritionPlans();
        // consturct locked state nutrition plans
        $this->lockingState->constructNutritionPlans($this->nutritionsPlanType->getDataTemp()[0]);

        $this->recepies();
        
        $this->workouts();
        // consturct locked state workouts
        //$this->lockingState->constructWorkouts($this->workoutsType->getDataTemp()[0]);

        $this->exercises();
        $this->tags();

        $biggestVersions = [
            $this->packageType->getDataTemp()[1],
            $this->workoutsPlanType->getDataTemp()[1],
            $this->nutritionsPlanType->getDataTemp()[1],
            $this->workoutsType->getDataTemp()[1],
            $this->recepiesType->getDataTemp()[1],
            $this->exercisesType->getDataTemp()[1],
            $this->tagsType->getDataTemp()[1]
        ];

        // remove workout duplicates
        $workoutTemp = [];
        $ids = [];
        $workouts = $this->workoutsType->getDataTemp()[0];
        foreach($workouts as $workout){

            $id = $workout['id'];

            if(!in_array($id, $ids) && $id != "0"){
                array_push($ids, $id);
                array_push($workoutTemp, $workout);
            }
        }

        // consturct locked state workouts
        $this->lockingState->constructWorkouts($workoutTemp);

        // remove exercise duplicates
        $exerciseTemp = [];
        $ids = [];
        $exercises = $this->exercisesType->getDataTemp()[0];
        foreach($exercises as $exercise){

            $id = $exercise['id'];

            if(!in_array($id, $ids)){
                array_push($ids, $id);
                array_push($exerciseTemp, $exercise);
            }
        }

        $response = [
            'version' => max($biggestVersions),
            'packages' => $this->packageType->getDataTemp()[0],
            'training_plans' => $this->workoutsPlanType->getDataTemp()[0],
            'nutrition_plans' => $this->nutritionsPlanType->getDataTemp()[0],
            'workouts' => $workoutTemp, // $this->workoutsType->getDataTemp()[0],
            'recipes' => $this->recepiesType->getDataTemp()[0],
            'exercises' => $exerciseTemp,
            'tags' => $this->tagsType->getDataTemp()[0]
        ];
        
        return $response;
    }
    
    
    /**
     * Handle Packages
     */
    public function packages()
    {
        $this->packageType->setDataRaw($this->rawData);
        $this->packageType->handleData();
    }
    
    
    /**
     * Handle Workout Plans
     */
    public function workoutPlans()
    {
        $this->workoutsPlanType->handleData();
    }
    
    
    /**
     * Handle Nutrition Plans
     */
    public function nutritionPlans()
    {
        $this->nutritionsPlanType->handleData();
    }
    
    
    public function workouts()
    {
        $this->workoutsType->handleData();
    }
    
    
    /**
     * Hanlde Recepies
     */
    public function recepies()
    {
        $this->recepiesType->handleData();
    }
    
    
    public function exercises()
    {
        $this->exercisesType->handleData();
    }
    
    
    /**
     * Handle Tags
     */
    public function tags()
    {
        $this->tagsType->handleData();
    }
    
}

