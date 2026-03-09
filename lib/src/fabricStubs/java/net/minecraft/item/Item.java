package net.minecraft.item;

public class Item {
    private final Settings settings;

    public Item(Settings settings) {
        this.settings = settings;
    }

    public Settings settings() {
        return settings;
    }

    public static class Settings {
        private int maxCount = 64;

        public Settings maxCount(int maxCount) {
            this.maxCount = maxCount;
            return this;
        }

        public int maxCount() {
            return maxCount;
        }
    }
}
