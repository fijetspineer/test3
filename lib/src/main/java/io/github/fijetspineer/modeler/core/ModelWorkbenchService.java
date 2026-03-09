package io.github.fijetspineer.modeler.core;

import io.github.fijetspineer.modeler.format.ObjCodec;
import io.github.fijetspineer.modeler.format.StlCodec;
import io.github.fijetspineer.modeler.mesh.MeshModel;
import io.github.fijetspineer.modeler.mesh.PrimitiveFactory;

import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

public final class ModelWorkbenchService {
    private final ObjCodec objCodec = new ObjCodec();
    private final StlCodec stlCodec = new StlCodec();

    public ModelWorkbenchSession newSession() {
        WorkbenchSettings settings = WorkbenchSettings.defaults();
        return new ModelWorkbenchSession(settings, PrimitiveFactory.create(settings.primitivePreset(), settings), "cube");
    }

    public ModelWorkbenchSession applyPreset(ModelWorkbenchSession session, PrimitivePreset preset) {
        WorkbenchSettings updatedSettings = new WorkbenchSettings(
                preset,
                session.settings().selectionMode(),
                session.settings().shadingMode(),
                session.settings().gridSize(),
                session.settings().snapEnabled(),
                session.settings().snapIncrement(),
                session.settings().symmetryX(),
                session.settings().symmetryY(),
                session.settings().symmetryZ(),
                session.settings().extrusionDepth(),
                session.settings().bevelAmount(),
                session.settings().rotationStepDegrees(),
                session.settings().scaleStep(),
                session.settings().livePreview(),
                session.settings().autoCenterImports(),
                session.settings().exportNormals(),
                session.settings().triangulateExports(),
                session.settings().importScale(),
                session.settings().exportScale(),
                session.settings().wireframeOverlay(),
                session.settings().radialSegments()
        );
        return new ModelWorkbenchSession(updatedSettings, PrimitiveFactory.create(preset, updatedSettings), preset.name().toLowerCase(Locale.ROOT));
    }

    public ModelWorkbenchSession importMesh(String fileName, byte[] content, WorkbenchSettings settings) {
        String lowerName = fileName.toLowerCase(Locale.ROOT);
        MeshModel mesh = lowerName.endsWith(".stl")
                ? stlCodec.read(stripExtension(fileName), content)
                : objCodec.read(stripExtension(fileName), new String(content, StandardCharsets.UTF_8));
        if (settings.autoCenterImports()) {
            mesh = mesh.centered();
        }
        if (settings.importScale() != 1.0) {
            mesh = mesh.scaled(settings.importScale());
        }
        return new ModelWorkbenchSession(settings, mesh, fileName);
    }

    public byte[] exportMesh(ModelWorkbenchSession session, ModelFormat format) {
        MeshModel mesh = session.mesh();
        if (session.settings().exportScale() != 1.0) {
            mesh = mesh.scaled(session.settings().exportScale());
        }
        return switch (format) {
            case OBJ -> objCodec.write(mesh, session.settings().triangulateExports()).getBytes(StandardCharsets.UTF_8);
            case STL -> stlCodec.writeAscii(mesh);
        };
    }

    public List<String> describeSettings(WorkbenchSettings settings) {
        List<String> lines = new ArrayList<>();
        lines.add("Primitive: " + settings.primitivePreset());
        lines.add("Selection mode: " + settings.selectionMode());
        lines.add("Shading: " + settings.shadingMode());
        lines.add("Grid size: " + settings.gridSize());
        lines.add("Snap: " + (settings.snapEnabled() ? settings.snapIncrement() : "off"));
        lines.add("Symmetry: X=" + settings.symmetryX() + ", Y=" + settings.symmetryY() + ", Z=" + settings.symmetryZ());
        lines.add("Extrusion depth: " + settings.extrusionDepth());
        lines.add("Bevel amount: " + settings.bevelAmount());
        lines.add("Rotation step: " + settings.rotationStepDegrees());
        lines.add("Scale step: " + settings.scaleStep());
        lines.add("Live preview: " + settings.livePreview());
        lines.add("Auto-center imports: " + settings.autoCenterImports());
        lines.add("Export normals: " + settings.exportNormals());
        lines.add("Triangulate exports: " + settings.triangulateExports());
        lines.add("Import scale: " + settings.importScale());
        lines.add("Export scale: " + settings.exportScale());
        lines.add("Wireframe overlay: " + settings.wireframeOverlay());
        lines.add("Radial segments: " + settings.radialSegments());
        return lines;
    }

    private String stripExtension(String fileName) {
        int index = fileName.lastIndexOf('.');
        return index > 0 ? fileName.substring(0, index) : fileName;
    }
}
