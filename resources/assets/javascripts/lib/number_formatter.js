export const numberFormatter = (number, digits) => {
    const lookup = [
        { value: 1, symbol: "" },
        { value: 1e3, symbol: "k" },
        { value: 1e6, symbol: "M" },
        { value: 1e9, symbol: "G" },
        { value: 1e12, symbol: "T" },
        { value: 1e15, symbol: "P" },
        { value: 1e18, symbol: "E" }
    ];
    const regexp = /\.0+$|(?<=\.[0-9]*[1-9])0+$/;
    const item = lookup.findLast(item => number >= item.value);

    if (!item) {
        return "0";
    }

    let formattedNumber = (number / item.value).toFixed(digits).replace(regexp, "").replace(".", ",");

    return formattedNumber + item.symbol;
}
