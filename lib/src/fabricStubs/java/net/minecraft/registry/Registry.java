package net.minecraft.registry;

import net.minecraft.util.Identifier;

import java.util.LinkedHashMap;
import java.util.Map;

public class Registry<T> {
    private final Map<Identifier, T> values = new LinkedHashMap<>();

    public T put(Identifier id, T value) {
        values.put(id, value);
        return value;
    }

    public static <T> T register(Registry<T> registry, Identifier id, T value) {
        return registry.put(id, value);
    }
}
