import AuctionMeta from "./AuctionMeta.vue";

export default {
  title: "Molecules/AuctionMeta",
  component: AuctionMeta,
  tags: ["autodocs"],
};

export const Default = {
  args: {
    seller: "Alex Morgan",
    endsAt: "2026-06-08T18:00:00+02:00",
  },
};
