package io.github.fijetspineer.modeler.mesh;

public record Vec3(double x, double y, double z) {
    public Vec3 add(Vec3 other) {
        return new Vec3(x + other.x, y + other.y, z + other.z);
    }

    public Vec3 scale(double factor) {
        return new Vec3(x * factor, y * factor, z * factor);
    }

    public Vec3 divide(double factor) {
        return new Vec3(x / factor, y / factor, z / factor);
    }
}
