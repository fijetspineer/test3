package io.github.fijetspineer.modeler.core;

import io.github.fijetspineer.modeler.mesh.MeshModel;

public record ModelWorkbenchSession(WorkbenchSettings settings, MeshModel mesh, String sourceName) {
    public ModelWorkbenchSession {
        if (settings == null || mesh == null) {
            throw new IllegalArgumentException("settings and mesh are required");
        }
        sourceName = sourceName == null ? mesh.name() : sourceName;
    }

    public ModelWorkbenchSession withSettings(WorkbenchSettings updatedSettings) {
        return new ModelWorkbenchSession(updatedSettings, mesh, sourceName);
    }

    public ModelWorkbenchSession withMesh(MeshModel updatedMesh) {
        return new ModelWorkbenchSession(settings, updatedMesh, sourceName);
    }
}
