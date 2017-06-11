from math import sqrt

from Simulation import Entity
from random import choice, random
from abc import ABCMeta, abstractmethod


def pick(seq):
    if not seq:
        return None
    return choice(seq)


class Animal(Entity, metaclass=ABCMeta):
    @abstractmethod
    def __init__(self):
        super().__init__()

    def get_next_position(self):
        return pick(self.world.neighbours(self.position))

    def move(self, position):
        if not position:
            return

        occupant = self.world.get(position)

        if occupant is not None and self.resolve_conflict(occupant):
            return

        self.world.move(self, position)

    def resolve_conflict(self, occupant):
        if occupant is self:
            return True

        try:
            if occupant.defend(self):
                return True
        except AttributeError:
            pass

        winner = self.fight(occupant)

        return winner is not self

    def action(self):
        self.move(self.get_next_position())

    def defend(self, attacker):
        if type(self) is type(attacker):
            self.breed(pick(self.world.neighbours(self.position)))
            return True

        return False

    def breed(self, position):
        if random() > .8:
            return

        return super().breed(position)


class Sheep(Animal):
    def __init__(self):
        super().__init__()

        self.color = "#eeeeee"
        self._strength = 4
        self._initiative = 4


class Wolf(Animal):
    def __init__(self):
        super().__init__()

        self._initiative = 5
        self._strength = 9
        self.color = "#666666"


class Turtle(Animal):
    def __init__(self):
        super().__init__()

        self._initiative = 1
        self._strength = 2
        self.color = "#006600"

    def action(self):
        if random() > .75:
            return

        super().action()

    def defend(self, entity):
        if super().defend(entity):
            return True

        if entity.strength >= 5:
            return False

        # todo: komunikat
        return True


class Antelope(Animal):
    def __init__(self):
        super().__init__()

        self._initiative = 4
        self._strength = 4
        self.color = "#ffff99"

    def get_next_position(self):
        position = pick(self.world.neighbours(super().get_next_position()))
        if position == self.position:
            return None

        return position

    def defend(self, attacker):
        position = pick([position for position in self.world.neighbours(self.position) if self.world.get(self.position) is None])
        self.move(position)


class Fox(Animal):
    def __init__(self):
        super().__init__()

        self._initiative = 7
        self._strength = 3
        self.color = "#ff6600"

    def get_next_position(self):
        return pick([
            x for x in self.world.neighbours(self.position)
            if self.world.get(x) is None or self.world.get(x).strength < self.strength
        ])


from Plants import Heracleum
class CyberSheep(Animal):
    def __init__(self):
        super().__init__()

        self._initiative = 5
        self._strength = 9
        self.color = "#8de5f4"

    def defend(self, attacker):
        if super().defend(attacker):
            return True

        return isinstance(attacker, Heracleum)

    def fight(self, enemy):
        if isinstance(enemy, Heracleum):
            enemy.kill()
            return self

        return super().fight(enemy)

    def get_next_position(self):
        heracleum = self.find_heracleum()

        if heracleum is not None:
            x, y = self.position
            return x + (self.ticks % 2) * ((x < heracleum.position[0]) - (x > heracleum.position[0])), \
                   y + ((self.ticks+1) % 2) * ((y < heracleum.position[1]) - (y > heracleum.position[1]))

        return super().get_next_position()

    def find_heracleum(self):
        def dist(a, b):
            x1, y1 = a
            x2, y2 = b

            return sqrt((x1 - x2)**2 + (y1 - y2)**2)

        distances = [(dist(pos, self.position), current) for pos, current in self.world.entities.items() if isinstance(current, Heracleum)]
        return None if not distances else min(distances)[1]


class Human(Animal):
    def __init__(self):
        super().__init__()

        self._strength = 5
        self._initiative = 4

        self.countdown = 0
        self.power = 0
        self.movement = (0, 0)

    @property
    def strength(self):
        return super().strength + self.power

    def superpower(self):
        self.power = 5
        self.countdown = self.power + 5

    def action(self):
        if self.power > 0:
            self.power -= 1
            self.world.messages.append("You have {} turns of superpower, power: {}".format(self.countdown, self.strength))

        if self.countdown > 0:
            self.countdown -= 1
            self.world.messages.append("You have {} turns to superpower".format(self.countdown))

        x, y = self.position
        dx, dy = self.movement

        newpos = (x + dx, y + dy)
        if not self.world.in_range(newpos):
            return

        self.move(newpos)
        self.movement = (0, 0)
