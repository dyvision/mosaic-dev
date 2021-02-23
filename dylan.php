<?php

//quick search and comparison function over foreach break function

# Idea is to take an array and only search by it's identifying column. In this case we're searching by user's id
# after that we're doing an array search which will return the position of the item in the array. For example: if $_GET['id'] equals "thatclassyfella"
# then it's going to search for my account in an array that looks like this: ['Bobe Dugand','Alec Cupelli',...,'thatclassyfella',...]
# the '...' is just saying there's more items before and after that account. So in this case the account 'thatclassyfella' will return the integer 15
# we then simply dig into the array directly going to the number 15 because we loaded the return into the $me variable.
# Now we can get comparative. We do a simple if else statement on the complimentary data that we're looking for. In this case "does thatclassyfella have more than 5 tracks in his list?"
# This type of lookup is ideal for doing some validation combination. For example if you're working on an account system and you don't want it to loop through every single person until
# it finds the matching id you can do this function below. An example would be doing a search by email as the main identifier and then after retrieving the user from the list checking
# the password in the if else statement and seeing if it matches.


//get array
$lists = json_decode(file_get_contents('lists.json'),true);

//search using main identifier
$me = array_search($_GET['id'],array_column($lists,'display_name'));

//perform a comparitive function on the item number that was returned
if(count($lists[$me]['tracks']) > 5){
    //fill the entire set of data into another variable
    $result = $lists[$me];
}else{
    $result = 'Not Found';
}

//return the user's data
print_r(json_encode($result));