package io.github.fijetspineer.modeler.mesh;

import java.util.List;

public record MeshFace(List<Integer> vertexIndices) {
    public MeshFace {
        vertexIndices = List.copyOf(vertexIndices);
        if (vertexIndices.size() < 3) {
            throw new IllegalArgumentException("A face needs at least three vertices");
        }
    }
}
