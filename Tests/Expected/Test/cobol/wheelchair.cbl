identification division.
program-id. helloworld.

data division.
working-storage section.

01 the-vars.
    03 COW-vars OCCURS 99 times.
       05 COW-varname       pic x(99).
       05 COW-varvalue      pic x(99).

linkage section.

01 the-values.
    05 COW-query-values           occurs 10 times.
       10 COW-query-value-name     pic x(90).
       10 COW-query-value          pic x(90).


procedure division using the-values.

    move "username" to COW-varname(1).
    move COW-query-value(1) to COW-varvalue(1).

    call 'template' using the-vars "hello.cow".

goback.
end program helloworld.