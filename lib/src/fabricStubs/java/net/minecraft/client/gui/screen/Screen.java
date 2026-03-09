package net.minecraft.client.gui.screen;

import net.minecraft.client.font.TextRenderer;
import net.minecraft.client.gui.DrawContext;
import net.minecraft.text.Text;

public abstract class Screen {
    protected final Text title;
    protected final TextRenderer textRenderer = new TextRenderer();

    protected Screen(Text title) {
        this.title = title;
    }

    public abstract void render(DrawContext context, int mouseX, int mouseY, float delta);
}
