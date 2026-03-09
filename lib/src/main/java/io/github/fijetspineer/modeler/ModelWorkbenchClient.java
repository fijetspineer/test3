package io.github.fijetspineer.modeler;

import io.github.fijetspineer.modeler.item.ModelingToolItem;
import io.github.fijetspineer.modeler.ui.ModelWorkbenchScreen;
import net.fabricmc.api.ClientModInitializer;

public final class ModelWorkbenchClient implements ClientModInitializer {
    private static ModelWorkbenchScreen previewScreen;

    @Override
    public void onInitializeClient() {
        if (ModelWorkbenchMod.MODELING_TOOL instanceof ModelingToolItem item) {
            previewScreen = item.createWorkbenchScreen();
        }
    }

    public static ModelWorkbenchScreen previewScreen() {
        return previewScreen;
    }
}
