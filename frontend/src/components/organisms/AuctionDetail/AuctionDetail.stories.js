import AuctionDetail from "./AuctionDetail.vue";

export default {
  title: "Organisms/AuctionDetail",
  component: AuctionDetail,
  tags: ["autodocs"],
};

export const Default = {
  args: {
    auction: {
      id: 1,
      title: "Vintage Mechanical Keyboard",
      seller: "Alex Morgan",
      category: "Electronics",
      endsAt: "2026-06-08T18:00:00+02:00",
      description: "A restored mechanical keyboard with original keycaps and a tested USB adapter.",
      startingPrice: 45,
      currentBid: 72.5,
      status: "open",
    },
  },
};
