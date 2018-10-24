<?php
namespace Model\Service\Facade\DataTypes;

use Model\Service\Facade\DataFacade;

class WorkoutsType
{
    
    private $parent;
    private $dataTemp = [];
    private $dataRaw = [];

    
    public function __construct(DataFacade $parent)
    {
        $this->parent = $parent;
    }
    
    
    public function getDataTemp():array
    {
        return $this->parent->tagsType->handleVersions($this->dataTemp);
    }
    
    
    public function setDataRaw($raw)
    {
        $this->dataRaw = array_merge($this->dataRaw, (array)$raw);
    }
    
    
    public function handleData()
    {
        // tags
        foreach($this->dataRaw as $data){                        
            // add tags
            $this->parent->tagsType->setDataRaw($data['tags']);

            // explode workout data
            $extractedData = $this->explodeWorkoutData($data);

            // return tags ids
            $extractedData['tags'] = $this->parent->tagsType->handleIds($data['tags']);

            // add kind
            $extractedData = array_merge($extractedData,["kind" => $this->parent->lockingState->workoutKind($data['id'])]);

            // add workout
            if(!in_array($extractedData, $this->dataTemp)){
                array_push($this->dataTemp, $extractedData);
            }

            // loop trough exercises
            foreach($data['rounds'] as $round){
                // add exercises
                $this->parent->exercisesType->setDataRaw($round['round_exercises']);
            }
        }
    }


    /**
     * Explode workout data
     *
     * @param $data
     * @return array
     */
    public function explodeWorkoutData($data){

        // set workout values
        $newData = [];
        $newData['id'] = $data['id'];
        $newData['name'] = $data['name'];
        $newData['description'] = $data['description'];
        // $newData['language'] = $data['language'];
        $newData['duration'] = $data['duration'];
        $newData['version'] = $data['version'];
        // $newData['state'] = $data['state'];

        // get rounds
        $rounds = $data['rounds'];
        $exercises = [];

        // loop through rounds
        foreach($rounds as $round){

            // set round values
            $roundNumber = $round['round'];
            $roundDuration = $round['duration'];
            $roundRestDuration = $round['rest_duration'];
            $roundType = $round['type'];
            $roundBehavior = $round['behavior'];
            $roundExercises = $round['round_exercises'];

            // loop through round exercises
            if(!empty($roundExercises)){
                foreach($roundExercises as $roundExercise){
                    
                    // set exercise values for workout section
                    $exercise['round'] = $roundNumber;
                    $exercise['exercise_u_id'] = $roundExercise['id'];
                    $exercise['type'] = $roundType;
                    $exercise['duration'] = $roundDuration;
                    $exercise['rest'] = $roundRestDuration;
                    $exercise['behavior'] = $roundBehavior;
                    
                    // $exercise['version'] = (int)$roundExercise['version'];
                    
                    if(!in_array($exercise, $exercises) && ($this->parent->version < (int)$roundExercise['version'])){
                        array_push($exercises, $exercise);
                    }
                }
            }

        }

        // add exercises to new data holder
        $newData['workout_exercises'] = $exercises;

        return $newData;
    }
    
}

