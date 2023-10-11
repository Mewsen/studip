import {PreRenderedAsset} from "rollup";

type AssetOutputEntry = {
    output: string,
    regex: RegExp
}

export const assetDir = "public/assets";
export const entryFileNames = `${assetDir}/javascripts/[name].js`;
export const chunkFileNames = `${assetDir}/javascripts/[hash].chunk.js`
const assets: AssetOutputEntry[] = [
    // {
    //     output: `${assetDir}/images/[name]-[hash][extname]`,
    //     regex: /\.(png|jpe?g|gif|svg|webp|avif)$/
    // },
    {
        regex: /\.css$/,
        output: `${assetDir}/stylesheets/[name][extname]`
    },
    {
        output: `${assetDir}/javascripts/[name][extname]`,
        regex: /\.js$/
    },
];

export function processAssetFileNames(info: PreRenderedAsset): string {
    if (info && info.name) {
        const name = info.name as string;
        console.log(name);
        const result = assets.find(a => a.regex.test(name));
        if (result) {
            return result.output;
        }
    }
    // default since we don't have an entry
    return `${assetDir}/[name][extname]`
}
