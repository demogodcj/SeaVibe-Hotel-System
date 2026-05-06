<?php
function calculateTotalPrice($roomType, $bed, $meal, $noDays) {
    // Room base prices
    $roomPrices = [
        "Superior Room" => 900,
        "Deluxe Room"   => 950,
        "Guest House"   => 800,
        "Single Room"   => 700
    ];

    // Bed extra charges
    $bedPrices = [
        "Single" => 0,
        "Double" => 200,
        "Triple" => 400,
        "Quad"   => 600
    ];

    // Meal extra charges (per day per room)
    $mealPrices = [
        "Room only"  => 0,
        "Breakfast"  => 100,
        "Half Board" => 200,
        "Full Board" => 300
    ];

    $roomCost = $roomPrices[$roomType] ?? 0;
    $bedCost  = $bedPrices[$bed] ?? 0;
    $mealCost = $mealPrices[$meal] ?? 0;

    return ($roomCost + $bedCost + $mealCost) * (int)$noDays;
}
?>
