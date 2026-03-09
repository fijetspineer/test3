package io.github.fijetspineer.modeler.mesh;

import java.util.ArrayList;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

public record MeshModel(String name, List<Vec3> vertices, List<MeshFace> faces) {
    public MeshModel {
        name = (name == null || name.isBlank()) ? "mesh" : name;
        vertices = List.copyOf(vertices);
        faces = List.copyOf(faces);
        if (vertices.isEmpty()) {
            throw new IllegalArgumentException("Mesh needs at least one vertex");
        }
        if (faces.isEmpty()) {
            throw new IllegalArgumentException("Mesh needs at least one face");
        }
    }

    public MeshModel scaled(double factor) {
        List<Vec3> scaledVertices = vertices.stream().map(vertex -> vertex.scale(factor)).toList();
        return new MeshModel(name, scaledVertices, faces);
    }

    public MeshModel translated(Vec3 delta) {
        List<Vec3> translatedVertices = vertices.stream().map(vertex -> vertex.add(delta)).toList();
        return new MeshModel(name, translatedVertices, faces);
    }

    public MeshModel centered() {
        Vec3 sum = new Vec3(0, 0, 0);
        for (Vec3 vertex : vertices) {
            sum = sum.add(vertex);
        }
        Vec3 centroid = sum.divide(vertices.size());
        return translated(new Vec3(-centroid.x(), -centroid.y(), -centroid.z()));
    }

    public List<MeshFace> triangulatedFaces() {
        List<MeshFace> triangles = new ArrayList<>();
        for (MeshFace face : faces) {
            List<Integer> indices = face.vertexIndices();
            for (int i = 1; i < indices.size() - 1; i++) {
                triangles.add(new MeshFace(List.of(indices.get(0), indices.get(i), indices.get(i + 1))));
            }
        }
        return triangles;
    }

    public static MeshModel deduplicate(String name, List<Vec3> vertices, List<MeshFace> faces) {
        Map<Vec3, Integer> uniqueVertices = new LinkedHashMap<>();
        List<MeshFace> remappedFaces = new ArrayList<>();
        for (MeshFace face : faces) {
            List<Integer> remapped = new ArrayList<>();
            for (Integer index : face.vertexIndices()) {
                Vec3 vertex = vertices.get(index);
                remapped.add(uniqueVertices.computeIfAbsent(vertex, unused -> uniqueVertices.size()));
            }
            remappedFaces.add(new MeshFace(remapped));
        }
        return new MeshModel(name, new ArrayList<>(uniqueVertices.keySet()), remappedFaces);
    }
}
