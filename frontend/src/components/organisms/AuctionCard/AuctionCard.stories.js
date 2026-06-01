import AuctionCard from "./AuctionCard.vue";

const sampleAuction = {
  id: 1,
  title: "Vintage Mechanical Keyboard",
  seller: "Alex Morgan",
  category: "Electronics",
  endsAt: "2026-06-08T18:00:00+02:00",
  description: "A restored mechanical keyboard with original keycaps and a tested USB adapter.",
  startingPrice: 45,
  currentBid: 72.5,
  status: "open",
};

export default {
  title: "Organisms/AuctionCard",
  component: AuctionCard,
  tags: ["autodocs"],
};

export const Default = {
  args: {
    auction: sampleAuction,
  },
};

export const Sports = {
  args: {
    auction: {
      ...sampleAuction,
      id: 2,
      title: "Road Bike With Aluminum Frame",
      seller: "Samira de Vries",
      category: "Sports",
      currentBid: 245,
    },
  },
};
