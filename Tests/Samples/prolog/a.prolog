nalezy( X,[X|_] ).
nalezy( X,[_|T] ) :- nalezy( X,T ).

usun(X,[X|T],T).
usun(X,[Y|T],[Y|T1]):- usun(X,T,T1).

find_leaf((X-Y), T) :-
    nalezy((A-B), T), nalezy((C-D), T), (A-B) \= (C-D),
    ((A is C; A is D); (B is C; B is D)),
    nalezy(X, [A, B, C, D]), nalezy(Y, [A, B, C, D]),
    \+((nalezy((F-X), T), F \= Y)),\+((nalezy((X-F), T), F \= Y)).

find_minimal_leaf((X-Y), T) :-
    find_leaf((X-Y), T),
    \+((find_leaf((X2-Y2), T), (X-Y) \= (X2-Y2), X > X2)).

prufer_code([(_-_)|[]], []).
prufer_code(T, [Y|R]) :-
    find_minimal_leaf((X-Y), T),
    (usun((X-Y), T, T2); usun((Y-X), T, T2)),
    prufer_code(T2, R), !.

prufer_code([(5-4),(1-2), (5-1),(1-3)], ODP).