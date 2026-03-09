package io.github.fijetspineer.modeler.item;

import io.github.fijetspineer.modeler.core.ModelWorkbenchService;
import io.github.fijetspineer.modeler.ui.ModelWorkbenchScreen;
import net.minecraft.item.Item;

public final class ModelingToolItem extends Item {
    private final ModelWorkbenchService service;

    public ModelingToolItem(Settings settings, ModelWorkbenchService service) {
        super(settings);
        this.service = service;
    }

    public ModelWorkbenchScreen createWorkbenchScreen() {
        return new ModelWorkbenchScreen(service.newSession(), service);
    }
}
