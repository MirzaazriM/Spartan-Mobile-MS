<?php

namespace Model\Contract;


use Model\Entity\DataIdsCollection;

interface GetData
{

    // interface functions for get data facade class

    public function getDataFacade();

    public function getIds();

    public function getPackagesIds();

    public function getNutritionIds();

    public function getNutritionPlansIds();

    public function getWorkoutPlanIds();

    public function getWorkoutIds();

    public function getExerciseIds();

    public function getTagIds();

    public function getContent();

    public function getPackages();

    public function getNutritions();

    public function getNutritionPlans();

    public function getWorkoutPlans();

    public function getWorkouts();

    public function getExercises();

    public function getTags();

}