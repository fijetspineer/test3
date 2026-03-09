package io.github.fijetspineer.modeler.mesh;

import io.github.fijetspineer.modeler.core.PrimitivePreset;
import io.github.fijetspineer.modeler.core.WorkbenchSettings;

import java.util.ArrayList;
import java.util.List;

public final class PrimitiveFactory {
    private PrimitiveFactory() {
    }

    public static MeshModel create(PrimitivePreset preset, WorkbenchSettings settings) {
        return switch (preset) {
            case CUBE -> cube();
            case CYLINDER -> cylinder(settings.radialSegments());
            case SPHERE -> sphere(settings.radialSegments());
            case PYRAMID -> pyramid();
            case PLANE -> plane();
        };
    }

    private static MeshModel cube() {
        return new MeshModel(
                "cube",
                List.of(
                        new Vec3(-0.5, -0.5, -0.5),
                        new Vec3(0.5, -0.5, -0.5),
                        new Vec3(0.5, 0.5, -0.5),
                        new Vec3(-0.5, 0.5, -0.5),
                        new Vec3(-0.5, -0.5, 0.5),
                        new Vec3(0.5, -0.5, 0.5),
                        new Vec3(0.5, 0.5, 0.5),
                        new Vec3(-0.5, 0.5, 0.5)
                ),
                List.of(
                        new MeshFace(List.of(0, 1, 2, 3)),
                        new MeshFace(List.of(4, 5, 6, 7)),
                        new MeshFace(List.of(0, 1, 5, 4)),
                        new MeshFace(List.of(1, 2, 6, 5)),
                        new MeshFace(List.of(2, 3, 7, 6)),
                        new MeshFace(List.of(3, 0, 4, 7))
                )
        );
    }

    private static MeshModel plane() {
        return new MeshModel(
                "plane",
                List.of(
                        new Vec3(-0.5, 0, -0.5),
                        new Vec3(0.5, 0, -0.5),
                        new Vec3(0.5, 0, 0.5),
                        new Vec3(-0.5, 0, 0.5)
                ),
                List.of(new MeshFace(List.of(0, 1, 2, 3)))
        );
    }

    private static MeshModel pyramid() {
        return new MeshModel(
                "pyramid",
                List.of(
                        new Vec3(-0.5, 0, -0.5),
                        new Vec3(0.5, 0, -0.5),
                        new Vec3(0.5, 0, 0.5),
                        new Vec3(-0.5, 0, 0.5),
                        new Vec3(0, 0.75, 0)
                ),
                List.of(
                        new MeshFace(List.of(0, 1, 2, 3)),
                        new MeshFace(List.of(0, 1, 4)),
                        new MeshFace(List.of(1, 2, 4)),
                        new MeshFace(List.of(2, 3, 4)),
                        new MeshFace(List.of(3, 0, 4))
                )
        );
    }

    private static MeshModel cylinder(int segments) {
        List<Vec3> vertices = new ArrayList<>();
        List<Integer> top = new ArrayList<>();
        List<Integer> bottom = new ArrayList<>();
        for (int i = 0; i < segments; i++) {
            double angle = (Math.PI * 2 * i) / segments;
            double x = Math.cos(angle) * 0.5;
            double z = Math.sin(angle) * 0.5;
            bottom.add(vertices.size());
            vertices.add(new Vec3(x, -0.5, z));
            top.add(vertices.size());
            vertices.add(new Vec3(x, 0.5, z));
        }

        List<MeshFace> faces = new ArrayList<>();
        faces.add(new MeshFace(top));
        List<Integer> reversedBottom = new ArrayList<>(bottom);
        java.util.Collections.reverse(reversedBottom);
        faces.add(new MeshFace(reversedBottom));
        for (int i = 0; i < segments; i++) {
            int next = (i + 1) % segments;
            faces.add(new MeshFace(List.of(bottom.get(i), bottom.get(next), top.get(next), top.get(i))));
        }
        return new MeshModel("cylinder", vertices, faces);
    }

    private static MeshModel sphere(int segments) {
        int rings = Math.max(3, segments / 2);
        List<Vec3> vertices = new ArrayList<>();
        List<MeshFace> faces = new ArrayList<>();
        for (int ring = 0; ring <= rings; ring++) {
            double v = (double) ring / rings;
            double phi = Math.PI * v;
            double y = Math.cos(phi) * 0.5;
            double radius = Math.sin(phi) * 0.5;
            for (int segment = 0; segment < segments; segment++) {
                double u = (double) segment / segments;
                double theta = Math.PI * 2 * u;
                vertices.add(new Vec3(Math.cos(theta) * radius, y, Math.sin(theta) * radius));
            }
        }
        for (int ring = 0; ring < rings; ring++) {
            for (int segment = 0; segment < segments; segment++) {
                int nextSegment = (segment + 1) % segments;
                int a = ring * segments + segment;
                int b = ring * segments + nextSegment;
                int c = (ring + 1) * segments + nextSegment;
                int d = (ring + 1) * segments + segment;
                if (ring == 0) {
                    faces.add(new MeshFace(List.of(a, c, d)));
                } else if (ring == rings - 1) {
                    faces.add(new MeshFace(List.of(a, b, c)));
                } else {
                    faces.add(new MeshFace(List.of(a, b, c, d)));
                }
            }
        }
        return MeshModel.deduplicate("sphere", vertices, faces);
    }
}
