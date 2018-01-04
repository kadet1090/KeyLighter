sum([A], A).
sum([A|T], S) :- sum(T, ST), S is ST + A.

max1([]).
max1([A|T]) :- ( A = 1; A = 0 ), max1(T). % elementem tablicy może być tylko 0 lub 1

steps(L1, L2, I) :-
    nth0(I1, L1, 0), nth0(I1, L2, 1), % indeksy, dla których w L1 jest 0, a w L2 1
    nth0(I2, L1, 1), nth0(I2, L2, 0), % indeksy, dla których w L1 jest 1, a w L2 0
    % I1 jest indeksem, który przeszedł z 0 w 1
    % I2 jest indeksem, który przeszedł z 1 w 0
    % możemy więc powiedzieć, że jedynka przeniosła się z I2 do I1
    % Różnica to zatem I1 - I2
    I is I1 - I2.

% czy plansza L1 poprawna, względem planszy L2?
correct(L1, L2) :-
    length(L1, LEN), length(L2, LEN), !, % L2 musi być tej samej długości co L1
    max1(L2),                            % Może zawierać tylko 0 i 1
    sum(L1, SUM),sum(L2, SUM).           % I suma wszystkich pionków musi być w obu równa

% gdy tylko jeden ruch, o 1 albo 2 miejsca
fullfils_rules(L1, L2) :-
    findall(S, steps(L1, L2, S), LIST),
    % LIST zawiera wszystkie możliwości prowadzące do przekształcenia L1 w L2
    % jeden ruch oznacza, że tylko jedna 1 zamieniła swoje miejsce.
    length(LIST, 1),
    % dodatkowo, jedynka może przesunąć się tylko o 1 lub 2 miejsca w prawo
    ( DIST = 1; DIST = 2 ), steps(L1, L2, DIST).

% możliwe ruchy to takie, które są poprawne i spełniają zasady
possible(L1, L2) :- correct(L1, L2), fullfils_rules(L1, L2).

% ruch jest kończący, jeżeli nie ma po nim innych ruchów
final(S) :- \+possible(S, _).

% strategia jest wygrywająca, jeżeli istnieje taki ruch, że jego rezultatem będzie
% ruch końcowy, lub nie ma strategi wygrywającej z następnego ruchu.
winning(S)      :- possible(S, NEXT), (final(NEXT); \+winning(NEXT)), !.
