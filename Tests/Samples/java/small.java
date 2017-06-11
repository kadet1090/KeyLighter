package net.kadet.studies.simulator.worlds;

import net.kadet.studies.simulator.Entity;
import net.kadet.studies.simulator.World;
import net.kadet.studies.simulator.animals.Human;
import net.kadet.studies.simulator.helpers.Logger;
import net.kadet.studies.simulator.helpers.PolishLogger;
import net.kadet.studies.simulator.helpers.Vector2;
import net.kadet.studies.simulator.listeners.WorldActionListener;
import net.kadet.studies.simulator.maps.AbstractGameMap;

import java.awt.*;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.util.*;
import java.util.stream.Stream;

public abstract class AbstractWorld implements World {
    protected final int width;
    protected final int height;

    protected int turn;

    protected transient AbstractGameMap map;
    protected Human player;

    private Hashtable<Vector2, Entity> entities = new Hashtable<>();
    private transient Logger logger = new PolishLogger();

    public AbstractWorld(int width, int height) {
        this.width = width;
        this.height = height;

        player = new Human();
        player.setWorld(this);

        Random randomizer = new Random();

        place(player, new Vector2(randomizer.nextInt(width), randomizer.nextInt(height)));
    }

    @Override
    public int getWidth() {
        return width;
    }

    @Override
    public int getHeight() {
        return height;
    }

    @Override
    public int getTurn() {
        return turn;
    }

    @Override
    public Human getPlayer() { return player; }

    @Override
    public abstract Stream<Vector2> getNeighbours(Vector2 position);

    @Override
    public void addActionListener(WorldActionListener listener) {
        map.addMapActionListener(listener);
    }

    @Override
    public boolean isOccupied(Vector2 position) {
        return entities.containsKey(position);
    }

    @Override
    public Entity get(Vector2 position) {
        return entities.get(position);
    }

    @Override
    public void place(Entity entity, Vector2 position) {
        entities.values().removeAll(Collections.singleton(entity));
        entities.put(position, entity);

        entity.setPosition(position);
    }

    @Override
    public Component getComponent() {
        return this.map;
    }

    @Override
    public Logger getLogger() {
        return logger;
    }

    @Override
    public void update() {
        turn++;

        PriorityQueue<Entity> queue = new PriorityQueue<>(
                (o1, o2) -> -(o1.getInitiative() > o2.getInitiative() ? 1 : Integer.compare(o1.getTicks(), o2.getTicks()))
        );

        for(Entity entity : entities.values()) {
            if(entity != null) {
                queue.add(entity);
            }
        }

        if(player != null && !player.isAlive()) player = null;

        while (queue.size() != 0) {
            Entity current = queue.remove();
            current.update();
        }
        entities.values().removeIf(entity -> !entity.isAlive());

        this.map.repaint();
    }

    public boolean inRange(Vector2 pos) {
        return pos.x >= 0 && pos.y >= 0 && pos.x < width && pos.y < height;
    }

    private void readObject(ObjectInputStream in) throws IOException, ClassNotFoundException {
        in.defaultReadObject();
        logger = new PolishLogger();
    }
}
