<?php

final class AddDefaultStockImages extends Migration
{
    public function description()
    {
        return 'add first set of studip default stock images';
    }

    public function up()
    {
        $csv_string = ";filename;title;description;author;source;license
        ;StudIP-Bilderpool-1;Vintage arabesque interior;Vintage arabesque interior lithograph plate no. 57 & 58, Emile Prisses d’Avennes, La Decoration Arabe. Digitally enhanced from own original 1885 edition of the book;Emile Prisses d’Avennes;https://www.rawpixel.com/image/6241498/image-flower-aesthetic-vintage;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-2;Launch Control Center, Apollo 11 mission;Members of the Kennedy Space Center government-industry team rise from their consoles within the Launch Control Center to watch the Apollo 11 liftoff through a window.;NASA;https://www.rawpixel.com/image/1207195/moon-landing-photograph;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-3;Astronaut on EVA;Astronaut Edward H. White II, pilot on the Gemini-Titan IV (GT-4) spaceflight, floats in the zero gravity of space outside the Gemini IV spacecraft. Original from NASA . Digitally enhanced by rawpixel.;NASA;https://www.rawpixel.com/image/441361/free-photo-image-astronaut-nasa-space;https://www.usa.gov/government-copyright
        ;StudIP-Bilderpool-4;Raspberry;Frambozen (Raspberry) chromolithograph plates by Abraham Jacobus Wendel. Digitally enhanced from our own 1879 edition plates of Nederlandsche flora en pomona.      ;Abraham Jacobus Wendel;https://www.rawpixel.com/image/10209579/image-plant-strawberry-art;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-5;Human nervous system;An antique illustration of a human nervous system and muscular system (1900) by Larousse, Pierre; Augé and Claude. Digitally enhanced from our own original plate.;Larousse, Pierre; Augé and Claude;https://www.rawpixel.com/image/431531/free-illustration-image-anatomy-nervous-system-art-nouveau;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-6;The White Puppy;The White Puppy Book by Cecil Aldin (1910), a white dog ‘Rags’ running emotionally distressed. Digitally enhanced from our own original plate.;Cecil Aldin;https://www.rawpixel.com/image/431486/free-illustration-image-dog-retro-puppy;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-7;Crimson rosella bird;Crimson rosella bird painting. Digitally enhanced from our own 1842 edition of Le Jardin Des Plantes by Paul Gervais.;Paul Gervais;https://www.rawpixel.com/image/6318200/image-plant-aesthetic-watercolor;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-8;Hippopotamus;Hippopotamus (Hippopotame Amphibie) illustrated by Charles Dessalines D' Orbigny (1806-1876). Digitally enhanced from our own 1892 edition of Dictionnaire Universel D'histoire Naturelle.;Charles Dessalines D' Orbigny;https://www.rawpixel.com/image/325047/free-illustration-image-hippopotamus-ancient-animal;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-9;Attacus Atlas Moth;Attacus Atlas Moth (Attacus Aurora) illustrated by Charles Dessalines D' Orbigny (1806-1876). Digitally enhanced from our own 1892 edition of Dictionnaire Universel D'histoire Naturelle.;Charles Dessalines D' Orbigny;https://www.rawpixel.com/image/324048/free-illustration-image-butterfly-moth-insects;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-10;Apple Painting;Vintage illustration of apple digitally enhanced from vintage edition of The Fruit Grower's Guide (1891) by John Wright;John Wright;https://www.rawpixel.com/image/50962/apple-painting;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-11;The Great Wave off Kanagawa;Hokusai's Under the Wave off Kanagawa (1830-1833) vintage Japanese woodcut print. Original public domain image from The Minneapolis Institute of Art. Digitally enhanced by rawpixel.;Katsushika Hokusai;https://www.rawpixel.com/image/7661037/image-art-vintage-public-domain;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-12;Umezawa Manor;Katsushika Hokusai's Umezawa Manor in Sagami Province (1830–1833) vintage woodblock print. Original public domain image from the Minneapolis Institute of Art. Digitally enhanced by rawpixel.;Katsushika Hokusai;https://www.rawpixel.com/image/7661248/image-background-cloud-art;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-13;South Wind, Clear Sky;South Wind, Clear Sky, Part of the series Thirty-six Views of Mount Fuji, no. 33; Woodblock made ca. 1930;Katsushika Hokusai;https://commons.wikimedia.org/wiki/File:Red_Fuji_southern_wind_clear_morning.jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-14;From Room 3003–The Shelton;From Room 3003–The Shelton, New York, Looking Northeast (1927) by Alfred Stieglitz. Original from The Art Institute of Chicago. Digitally enhanced by rawpixel.;Alfred Stieglitz;https://www.rawpixel.com/image/3809173/photo-image-art-vintage-city;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-15;Family & Friends at Mittenwalk;Family & Friends at Mittenwalk (ca. 1884) by Alfred Stieglitz.Original public domain image from the Art Institute of Chicago;Alfred Stieglitz;https://www.rawpixel.com/image/7722311/photo-image-art-vintage-public-domain;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-16;A Wet Day on the Boulevard;A Wet Day on the Boulevard, Paris (1894) by Alfred Stieglitz. Original from The Art Institute of Chicago. Digitally enhanced by rawpixel.;Alfred Stieglitz;https://www.rawpixel.com/image/3809204/photo-image-art-people-vintage;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-17;Snowflake 332;Wilson Bentley's Snowflake 332 (ca. 1890) detailed photograph of snowflakes in high resolution by Wilson Alwyn Bentley. Original from The Smithsonian. Digitally enhanced by rawpixel.;Wilson Alwyn Bentley;https://www.rawpixel.com/image/2798916/free-photo-image-snowflake-winter-crystal;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-18;Adiantum pedatum ;Adiantum pedatum (American Maiden-hair Fern) young fronds enlarged 8 times from Urformen der Kunst (1928) by Karl Blossfeldt. Original from The Rijksmuseum. Digitally enhanced by rawpixel.;Karl Blossfeldt;https://www.rawpixel.com/image/2208515/karl-blossfeldt-macrophotography;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-19;Phacelia Tanacetifolia;Phacelia Tanacetifolia (Lacy Phacelia) enlarged 4 times from Urformen der Kunst (1928) by Karl Blossfeldt. Original from The Rijksmuseum. Digitally enhanced by rawpixel.;Karl Blossfeldt;https://www.rawpixel.com/image/2222798/karl-blossfeldt-macrophotography;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-20;Casino Pier at boardwalk;Casino Pier at boardwalk, Seaside Heights, New Jersey (1978) photography in high resolution by John Margolies. Original from the Library of Congress. Digitally enhanced by rawpixel.;John Margolies;https://www.rawpixel.com/image/3803766/photo-image-blue-people-vintage;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-21;Gingerbread People;Gingerbread People, Enchanted Forest, Route 40, Ellicott City, Maryland (1977) photography in high resolution by John Margolies. Original from the Library of Congress. Digitally enhanced by rawpixel.;John Margolies;https://www.rawpixel.com/image/3801492/photo-image-blue-vintage-cute;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-22;Dog Bark Park Bed & Breakfas;Dog Bark Park Bed & Breakfast, Cottonwood, Idaho (2004) photography in high resolution by John Margolies. Original from the Library of Congress. Digitally enhanced by rawpixel.;John Margolies;https://www.rawpixel.com/image/3803739/photo-image-blue-vintage-retro;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-23;Firefighter training;Firefighter training, fire service college;cheshirefireservice;https://www.rawpixel.com/image/7426610/photo-image-public-domain-fire-person;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-24;Audio mixer;Audio mixer;cheshirefireservice;https://www.rawpixel.com/image/7426672/photo-image-public-domain-technology;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-25;Bitter fit test;Bitter fit test solution. Original public domain image from Flickr;-;https://www.rawpixel.com/image/7426555/photo-image-public-domain-purple;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-26;Cityscape;A black-and-white cityscape of New York;Tom Adams;https://commons.wikimedia.org/wiki/File:Monochrome_New_York_cityscape_(Unsplash).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-27;ocean washing on the sand beach in California;Drone aerial view of the ocean washing on the sand beach in California Original public domain image from Wikimedia Commons;Sasha;https://commons.wikimedia.org/wiki/File:California_(Unsplash).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-28;Water surface;Turbulent Water surface;Christoffer Engström;https://commons.wikimedia.org/wiki/File:Christoffer_Engstr%C3%B6m_2017-01-13_(Unsplash_wc9avd2RaN0).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-29;Star Night Sky Ravine;Looking upward to the starry night sky from a ravine.;Mark Basarab;https://commons.wikimedia.org/wiki/File:Star_Night_Sky_Ravine_(Unsplash).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-30;Shanghai skyline; Original public domain image from Wikimedia Commons;Adi Constantin;https://commons.wikimedia.org/wiki/File:Shanghai_skyline_unsplash.jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-31;Pioneer plaque;NASA image of Pioneer 10's famed Pioneer plaque features a design engraved into a gold-anodized aluminum plate, 152 by 229 millimeters (6 by 9 inches), attached to the spacecraft's antenna support struts to help shield it from erosion by interstellar dust. - Photograph by NASA Ames Research Center (NASA-ARC);Designed by Carl Sagan and Frank Drake. Artwork prepared by Linda Salzman Sagan.;https://commons.wikimedia.org/wiki/File:Pioneer10-plaque.jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-32;Umbrella in the sky;Umbrellas sky. Original public domain image from Wikimedia Commons;StockSnap;https://commons.wikimedia.org/wiki/File:Umbrellas-2618715.jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-33;Peacock portrait;Peacock portrait. The term peacock is commonly used to refer to birds of both sexes. Technically, only males are peacocks. Females are peahens, and together, they are called peafowl.;Bernard Spragg;https://www.flickr.com/photos/volvob12b/8316435538/;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-34;Computer Chip;Computer chip;Fritzchens Fritz;https://www.flickr.com/photos/130561288@N04/52192565393/;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-35;Seaside town in Italy, Manarola, Italy ;Seaside town in Italy, Manarola, Italy.;Erin Maturo;https://commons.wikimedia.org/wiki/File:Erin_Maturo_2017_(Unsplash).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-36;Rose Flower;Rose Flower with Dewdrops;Bessi;https://commons.wikimedia.org/wiki/File:Rose_729509.jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-37;Waves, Antelope Canyon, USA;Waves, Antelope Canyon, USA.;Christopher Burns;https://commons.wikimedia.org/wiki/File:Waves_(Unsplash_C1J_eSGNPt0).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-38;Banana parade;Banana halves with dropshadow on blue background, surreal;Stux;https://pixabay.com/photos/surreal-trend-2016-gorgeous-1136080/;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-39;Waved facade detail of a building;Waved facade detail of a building;Ricardo Gomez Angel;https://commons.wikimedia.org/wiki/File:Ricardo_Gomez_Angel_2017-02-08_(Unsplash).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-40;Wall of books;Wall around a green wooden door covered with books, looking aged and weathered;pxhere.com;https://pxhere.com/en/photo/1409043;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-41;Hieroglyphics on gold metal;Hieroglyphics on gold metal;pxhere.com;https://pxhere.com/en/photo/928642;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-42;On a boat on Lago di Braies;On a boat on Lago di Braies, Italy;Luca Bravo;https://commons.wikimedia.org/wiki/File:Lago_di_Braies_on_boat.jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-43;Mammoth skeleton;Mammoth skeleton on display in a museum;Efraimstochter;https://pixabay.com/photos/mammoth-skeleton-museum-exhibit-1257288/;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-44;Space Nebula;Image of a nebula taken using a NASA telescope - Original from NASA. Digitally enhanced by rawpixel.;NASA;https://www.rawpixel.com/image/440213/nebula;https://www.usa.gov/government-copyright
        ;StudIP-Bilderpool-45;Clockwork;Movement detail of an automatic watch;Romain Guy;https://www.flickr.com/photos/romainguy/50785385016/;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-46;Getty center architecture;An arched gray terrace reflected in a window;Armando Castillejos;https://commons.wikimedia.org/wiki/File:Getty_center_architecture_(Unsplash).jpg;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-47;Autumn leaves;Colorful autumn leaves;Bluemorphos;https://pixabay.com/photos/autumn-leaves-fall-leaves-leaves-1486062/;https://creativecommons.org/publicdomain/zero/1.0/
        ;StudIP-Bilderpool-48;Spiral Staircase;Spiral stairway in building, low angle view of spiral stairway with pink lights - going up;Johannes Plenio;https://freerangestock.com/photos/143898/spiral-stairway-in-building--interior-design.html;https://creativecommons.org/publicdomain/zero/1.0/";
        $lines = explode( "\n", $csv_string );
        $headers = str_getcsv( array_shift( $lines ), ';' );
        $files = array();
        foreach ( $lines as $line ) {
            $row = array();
            foreach ( str_getcsv( $line, ';' ) as $key => $field ) {
                $row[ $headers[ $key ] ] = $field;
            }
            $row = array_filter( $row );
            $files[] = $row;
        }

        $dir = $GLOBALS['STUDIP_BASE_PATH'] . '/public/assets/images/default-stock-images/';
        for ($i = 0; $i < sizeof($files); $i++) {
            $meta = $files[$i];
            $filename = $i + 1 . '.webp';
            $filepath = $dir . $filename;
            $filesize = filesize($filepath);
            $imagesize = getimagesize($filepath);
            $image = StockImage::create([
                'title' => $meta['title'],
                'description' => $meta['description'] ?? '',
                'license' => $meta['license'] ?? '',
                'author' => $meta['author'] ?? '',
                'height' => $imagesize[1],
                'width' => $imagesize[0],
                'mime_type' => $imagesize['mime'],
                'size' => $filesize,
                'tags' => '["Stud.IP 5"]',
            ]);
            copy($filepath, $image->getPath());
            $scaler = new Studip\StockImages\Scaler();
            $scaler($image);
            $paletteCreator = new Studip\StockImages\PaletteCreator();
            $paletteCreator($image);
        }

        echo sizeof($files) . ' images have been added';
    }

    public function down()
    {
        $images = StockImage::findBySQL('tags = ?', ['["Stud.IP 5"]']);
        foreach($images as $image) {
            $image->delete();
        }
    }
}