package io.github.fijetspineer.modeler.format;

import io.github.fijetspineer.modeler.mesh.MeshFace;
import io.github.fijetspineer.modeler.mesh.MeshModel;
import io.github.fijetspineer.modeler.mesh.Vec3;

import java.util.ArrayList;
import java.util.List;

public final class ObjCodec {
    public MeshModel read(String name, String content) {
        List<Vec3> vertices = new ArrayList<>();
        List<MeshFace> faces = new ArrayList<>();
        for (String rawLine : content.split("\\R")) {
            String line = rawLine.trim();
            if (line.isEmpty() || line.startsWith("#")) {
                continue;
            }
            if (line.startsWith("v ")) {
                String[] parts = line.split("\\s+");
                vertices.add(new Vec3(
                        Double.parseDouble(parts[1]),
                        Double.parseDouble(parts[2]),
                        Double.parseDouble(parts[3])
                ));
            } else if (line.startsWith("f ")) {
                String[] parts = line.substring(2).trim().split("\\s+");
                List<Integer> indices = new ArrayList<>();
                for (String part : parts) {
                    String token = part.split("/")[0];
                    int parsed = Integer.parseInt(token);
                    int index = parsed > 0 ? parsed - 1 : vertices.size() + parsed;
                    indices.add(index);
                }
                faces.add(new MeshFace(indices));
            }
        }
        return new MeshModel(name, vertices, faces);
    }

    public String write(MeshModel mesh, boolean triangulate) {
        StringBuilder builder = new StringBuilder();
        builder.append("o ").append(mesh.name()).append('\n');
        for (Vec3 vertex : mesh.vertices()) {
            builder.append("v ")
                    .append(vertex.x()).append(' ')
                    .append(vertex.y()).append(' ')
                    .append(vertex.z()).append('\n');
        }
        List<MeshFace> faces = triangulate ? mesh.triangulatedFaces() : mesh.faces();
        for (MeshFace face : faces) {
            builder.append("f");
            for (Integer index : face.vertexIndices()) {
                builder.append(' ').append(index + 1);
            }
            builder.append('\n');
        }
        return builder.toString();
    }
}
