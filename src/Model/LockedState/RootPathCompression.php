<?php
namespace Model\LockedState;

class RootPathCompression
{
    
    
    /**
     * Find Workotu Roots
     * 
     * @param int $id
     * @param array $packages
     * @return array
     */
    public function findWorkoutPlanRoots(int $id, array $packages):array
    {
        $roots = [];
        
        foreach($packages as $package){
            
            if(in_array($id,$package['training_plans'])){
                array_push($roots, $package['sku']);
            }
      
        }

        return $roots;
    }
    
    
    /**
     * Find Nutrition Roots
     * 
     * @param int $id
     * @param array $packages
     * @return array
     */
    public function findNutritinoPlanRoots(int $id, array $packages):array
    {
        $roots = [];
        
        foreach($packages as $package){
            
            if(in_array($id,$package['nutrition_plans'])){
                array_push($roots, $package['sku']);
            }
            
        }
        
        return $roots;
    }
    
    
    /**
     * Find Workout Roots
     * 
     * @param int $id
     * @param array $packages
     * @param array $workoutPlans
     * @return array
     */
    public function findWorkoutRoots(int $id, array $packages, array $workoutPlans)
    {
        $plans = [];

        // take from workout plan roots 
        foreach($workoutPlans as $workout){
            
            if(in_array($id,$workout['workouts'])){
                array_push($plans, $workout['id']);
            }
            
        }

        $roots = [];
        // take from plan roots
        foreach($plans as $plan){
            $roots = array_merge($roots, $this->findWorkoutPlanRoots($plan, $packages));
        }

        return $roots;
    }
    
    
    /**
     * Analyze Consistency 
     * 
     * @param array $roots
     * @return boolean
     */
    public function analyzeKind(array $roots)
    {
        $isConsistent = true;
        foreach($roots as $root){
            foreach($roots as $rootPrim){
                if($root !==  $rootPrim){
                    $isConsistent = false;
                }
            }
        }

        return $isConsistent;
    }
    
    
    /**
     * Analyze Lock State
     * 
     * @param array $roots
     * @param string $type
     * @return string
     */
    public function analyzeLockState(array $roots, string $type): string
    {
        $state = "unlocked";

        if($type == 'pro' or $type == 'free'){
            if(!in_array($type, $roots)){
                if(in_array('free', $roots)){
                    $state = "unlocked";
                }else if(in_array('pro', $roots)){
                    $state = "locked";
                }else {
                    $state = "premium";
                }
            }
        }

        return $state;

//        $state = "unlocked";
//
//        if($type == 'pro' or $type == 'free'){
//            if(!in_array($type, $roots)){
//                if(in_array('free', $roots)){
//                    $state = "unlocked";
//                }else {
//                    $state = "locked";
//                }
//
//            }
//        }else {
//            $state = "locked";
//        }
//
//        return $state;
    }
    
}

