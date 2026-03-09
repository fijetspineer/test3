package io.github.fijetspineer.modeler.ui;

import io.github.fijetspineer.modeler.core.ModelWorkbenchService;
import io.github.fijetspineer.modeler.core.ModelWorkbenchSession;
import net.minecraft.client.gui.DrawContext;
import net.minecraft.client.gui.screen.Screen;
import net.minecraft.text.Text;

import java.util.ArrayList;
import java.util.List;

public final class ModelWorkbenchScreen extends Screen {
    private final ModelWorkbenchSession session;
    private final ModelWorkbenchService service;

    public ModelWorkbenchScreen(ModelWorkbenchSession session, ModelWorkbenchService service) {
        super(Text.literal("In-Game Model Workbench"));
        this.session = session;
        this.service = service;
    }

    public List<String> summaryLines() {
        List<String> lines = new ArrayList<>();
        lines.add("Source: " + session.sourceName());
        lines.add("Vertices: " + session.mesh().vertices().size());
        lines.add("Faces: " + session.mesh().faces().size());
        lines.addAll(service.describeSettings(session.settings()));
        lines.add("Import: OBJ / STL");
        lines.add("Export: OBJ / STL");
        return lines;
    }

    @Override
    public void render(DrawContext context, int mouseX, int mouseY, float delta) {
        int y = 18;
        context.drawText(textRenderer, title, 16, y, 0xFFFFFF, false);
        y += 14;
        for (String line : summaryLines()) {
            context.drawText(textRenderer, Text.literal(line), 16, y, 0xC8D0FF, false);
            y += 12;
        }
    }
}
