package io.github.fijetspineer.modeler.core;

public record WorkbenchSettings(
        PrimitivePreset primitivePreset,
        SelectionMode selectionMode,
        ShadingMode shadingMode,
        int gridSize,
        boolean snapEnabled,
        double snapIncrement,
        boolean symmetryX,
        boolean symmetryY,
        boolean symmetryZ,
        double extrusionDepth,
        double bevelAmount,
        double rotationStepDegrees,
        double scaleStep,
        boolean livePreview,
        boolean autoCenterImports,
        boolean exportNormals,
        boolean triangulateExports,
        double importScale,
        double exportScale,
        boolean wireframeOverlay,
        int radialSegments
) {
    public WorkbenchSettings {
        if (gridSize < 2) {
            throw new IllegalArgumentException("gridSize must be at least 2");
        }
        if (snapIncrement <= 0 || extrusionDepth <= 0 || rotationStepDegrees <= 0 || scaleStep <= 0) {
            throw new IllegalArgumentException("positive numeric settings are required");
        }
        if (importScale <= 0 || exportScale <= 0) {
            throw new IllegalArgumentException("import/export scale must be positive");
        }
        if (radialSegments < 3) {
            throw new IllegalArgumentException("radialSegments must be at least 3");
        }
    }

    public static WorkbenchSettings defaults() {
        return new WorkbenchSettings(
                PrimitivePreset.CUBE,
                SelectionMode.OBJECT,
                ShadingMode.SMOOTH,
                16,
                true,
                0.25,
                false,
                false,
                true,
                1.0,
                0.1,
                15.0,
                0.1,
                true,
                true,
                true,
                true,
                1.0,
                1.0,
                true,
                12
        );
    }
}
