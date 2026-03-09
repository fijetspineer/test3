package net.minecraft.client.gui;

import net.minecraft.client.font.TextRenderer;
import net.minecraft.text.Text;

public class DrawContext {
    public int drawText(TextRenderer renderer, Text text, int x, int y, int color, boolean shadow) {
        return text.toString().length();
    }
}
