package io.github.fijetspineer.modeler.format;

import io.github.fijetspineer.modeler.mesh.MeshFace;
import io.github.fijetspineer.modeler.mesh.MeshModel;
import io.github.fijetspineer.modeler.mesh.Vec3;

import java.io.ByteArrayInputStream;
import java.io.DataInputStream;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.nio.ByteOrder;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.List;

public final class StlCodec {
    public MeshModel read(String name, byte[] bytes) {
        if (looksAscii(bytes)) {
            return readAscii(name, new String(bytes, StandardCharsets.UTF_8));
        }
        return readBinary(name, bytes);
    }

    public byte[] writeAscii(MeshModel mesh) {
        StringBuilder builder = new StringBuilder();
        builder.append("solid ").append(mesh.name()).append('\n');
        for (MeshFace triangle : mesh.triangulatedFaces()) {
            builder.append("  facet normal 0 0 0\n");
            builder.append("    outer loop\n");
            for (Integer index : triangle.vertexIndices()) {
                Vec3 vertex = mesh.vertices().get(index);
                builder.append("      vertex ")
                        .append(vertex.x()).append(' ')
                        .append(vertex.y()).append(' ')
                        .append(vertex.z()).append('\n');
            }
            builder.append("    endloop\n");
            builder.append("  endfacet\n");
        }
        builder.append("endsolid ").append(mesh.name()).append('\n');
        return builder.toString().getBytes(StandardCharsets.UTF_8);
    }

    private MeshModel readAscii(String name, String content) {
        List<Vec3> vertices = new ArrayList<>();
        List<MeshFace> faces = new ArrayList<>();
        List<Integer> currentFace = new ArrayList<>();
        for (String rawLine : content.split("\\R")) {
            String line = rawLine.trim();
            if (line.startsWith("vertex ")) {
                String[] parts = line.split("\\s+");
                currentFace.add(vertices.size());
                vertices.add(new Vec3(
                        Double.parseDouble(parts[1]),
                        Double.parseDouble(parts[2]),
                        Double.parseDouble(parts[3])
                ));
            } else if (line.equals("endloop") && !currentFace.isEmpty()) {
                faces.add(new MeshFace(currentFace));
                currentFace = new ArrayList<>();
            }
        }
        return MeshModel.deduplicate(name, vertices, faces);
    }

    private MeshModel readBinary(String name, byte[] bytes) {
        List<Vec3> vertices = new ArrayList<>();
        List<MeshFace> faces = new ArrayList<>();
        try (DataInputStream input = new DataInputStream(new ByteArrayInputStream(bytes))) {
            input.skipBytes(80);
            int triangleCount = Integer.reverseBytes(input.readInt());
            for (int i = 0; i < triangleCount; i++) {
                readLittleEndianFloat(input);
                readLittleEndianFloat(input);
                readLittleEndianFloat(input);
                List<Integer> indices = new ArrayList<>();
                for (int vertex = 0; vertex < 3; vertex++) {
                    double x = readLittleEndianFloat(input);
                    double y = readLittleEndianFloat(input);
                    double z = readLittleEndianFloat(input);
                    indices.add(vertices.size());
                    vertices.add(new Vec3(x, y, z));
                }
                faces.add(new MeshFace(indices));
                input.skipBytes(2);
            }
        } catch (IOException exception) {
            throw new IllegalArgumentException("Unable to read STL", exception);
        }
        return MeshModel.deduplicate(name, vertices, faces);
    }

    private boolean looksAscii(byte[] bytes) {
        String header = new String(bytes, 0, Math.min(bytes.length, 256), StandardCharsets.UTF_8).trim().toLowerCase();
        return header.startsWith("solid") && header.contains("facet");
    }

    private float readLittleEndianFloat(DataInputStream input) throws IOException {
        byte[] buffer = input.readNBytes(4);
        if (buffer.length != 4) {
            throw new IOException("Unexpected end of file");
        }
        return ByteBuffer.wrap(buffer).order(ByteOrder.LITTLE_ENDIAN).getFloat();
    }
}
