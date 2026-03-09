package io.github.fijetspineer.modeler;

import io.github.fijetspineer.modeler.core.ModelFormat;
import io.github.fijetspineer.modeler.core.ModelWorkbenchService;
import io.github.fijetspineer.modeler.core.ModelWorkbenchSession;
import io.github.fijetspineer.modeler.core.WorkbenchSettings;
import org.junit.jupiter.api.Test;

import java.nio.charset.StandardCharsets;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

class ModelWorkbenchServiceTest {
    private final ModelWorkbenchService service = new ModelWorkbenchService();

    @Test
    void importsObjAndRecentersMesh() {
        String obj = """
                v 2 0 0
                v 4 0 0
                v 4 2 0
                f 1 2 3
                """;

        ModelWorkbenchSession session = service.importMesh("triangle.obj", obj.getBytes(StandardCharsets.UTF_8), WorkbenchSettings.defaults());

        assertEquals("triangle.obj", session.sourceName());
        assertEquals(3, session.mesh().vertices().size());
        assertEquals(0.0, session.mesh().vertices().stream().mapToDouble(v -> v.x()).average().orElseThrow(), 0.0001);
    }

    @Test
    void exportsTriangulatedObj() {
        ModelWorkbenchSession session = service.newSession();

        String exported = new String(service.exportMesh(session, ModelFormat.OBJ), StandardCharsets.UTF_8);

        assertTrue(exported.contains("o cube"));
        assertTrue(exported.contains("f 1 2 3"));
    }

    @Test
    void importsAndExportsAsciiStl() {
        byte[] stl = """
                solid tri
                  facet normal 0 0 0
                    outer loop
                      vertex 0 0 0
                      vertex 1 0 0
                      vertex 0 1 0
                    endloop
                  endfacet
                endsolid tri
                """.getBytes(StandardCharsets.UTF_8);

        ModelWorkbenchSession session = service.importMesh("tri.stl", stl, WorkbenchSettings.defaults());
        byte[] exported = service.exportMesh(session, ModelFormat.STL);
        String text = new String(exported, StandardCharsets.UTF_8);

        assertEquals(3, session.mesh().vertices().size());
        assertTrue(text.startsWith("solid tri"));
        assertTrue(text.contains("facet normal 0 0 0"));
        assertTrue(text.contains("vertex "));
    }
}
