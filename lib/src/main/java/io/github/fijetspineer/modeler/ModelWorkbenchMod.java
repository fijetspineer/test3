package io.github.fijetspineer.modeler;

import io.github.fijetspineer.modeler.core.ModelWorkbenchService;
import io.github.fijetspineer.modeler.item.ModelingToolItem;
import net.fabricmc.api.ModInitializer;
import net.minecraft.item.Item;
import net.minecraft.registry.Registries;
import net.minecraft.registry.Registry;
import net.minecraft.util.Identifier;

public final class ModelWorkbenchMod implements ModInitializer {
    public static final String MOD_ID = "ingamemodeler";
    public static final ModelWorkbenchService WORKBENCH_SERVICE = new ModelWorkbenchService();
    public static final Item MODELING_TOOL = Registry.register(
            Registries.ITEM,
            Identifier.of(MOD_ID, "modeling_tool"),
            new ModelingToolItem(new Item.Settings().maxCount(1), WORKBENCH_SERVICE)
    );

    @Override
    public void onInitialize() {
        // Registration is completed by static initialization.
    }
}
