package net.minecraft.text;

public final class Text {
    private final String value;

    private Text(String value) {
        this.value = value;
    }

    public static Text literal(String value) {
        return new Text(value);
    }

    @Override
    public String toString() {
        return value;
    }
}
