#!/bin/bash
# Arg 1 - working directory
cd $1
# Arg 2 - program to run
# Connect stdin (&0) to stdin of the program
# Connect stdout (&1) to stdout of program
# Connect stderr (&2) to stderr of program
# Run program in the background
printf "\n" >out.txt
/usr/bin/time -f "%e" -o out.txt -a timeout ${3}s $2 <&0 2>&2 > output.txt 
cat out.txt>>output.txt
head -c -1 output.txt
#bc <<< "scale=4;$sum/${4}">> output.txt
i=$?
#cat out.txt #>&1 # Workout $? returning the output code of tee
if [ $i -eq 124 ]; then
	echo "Time limit exceeded" >&2
fi 


#$2 <&0 >&1 2>&2 &
## Get the PID of the last program
#i=$!
#count=0
#ps aux | grep $i | grep -v grep >/dev/null
#running=$?
#while [ "$running" -eq "0" ] && [ "$count" -lt "$3" ]
#do
#    ps aux | grep $i | grep -v grep >/dev/null
#    running=$?
#    let "count=count+1"
#    sleep 1
#done
# Arg 3 - the amount of time in sec to wait before killing the program

# Try kill the program, if kill is successful, echo that it was running
# TODO: Make sure that the PID isn't reused!!!
#kill $i 2>/dev/null && echo -n "Time limit exceeded" >&2
