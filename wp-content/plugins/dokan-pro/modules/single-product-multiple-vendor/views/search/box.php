<?php
/**
 * Search Box template.
 * this will be displayed before add new product form.
 *
 * @sience 3.5.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<div class="dokan-spmv-add-new-product-search-box-area dokan-w13 section-closed">
    <div class="control-section">
        <span class="badge badge-optional"><?php esc_html_e( 'Optional', 'dokan' ); ?></span>
        <span class="badge badge-caret" id="dokan-spmv-area-toggle-button"><i class="fa fa-caret-down" aria-hidden="true"></i><i class="fa fa-caret-up" aria-hidden="true"></i></span>
    </div>
    <div class="info-section">
        <svg class="search-svg" viewBox="0 0 101 49" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.5">
                <rect x="27.8826" y="10.8305" width="41.624" height="25.7" rx="0.79" fill="#FF9B63"/>
                <rect x="29.6968" y="12.6446" width="38.1973" height="22.1726" rx="1.48" fill="#F2F2F2"/>
                <path d="M69.5067 42.0736H27.6812V41.533C27.6812 39.7307 29.1637 38.2438 30.9607 38.2438H66.2272C68.0242 38.2438 69.5067 39.7307 69.5067 41.533V42.0736Z" fill="#FF9B63" fill-opacity="0.23"/>
                <path d="M76.5547 48.6678L58.8025 33.8736L61.3178 30.8554L79.07 45.6496L76.5547 48.6678Z" fill="url(#paint0_linear_148:28)"/>
                <path d="M59.1058 33.5103L58.1575 32.72L60.0665 30.4293L61.0148 31.2196L59.1058 33.5103Z" fill="#E66947"/>
                <path d="M61.1816 29.2219C57.2157 36.051 48.4645 38.3724 41.6355 34.4065C34.8064 30.4407 32.485 21.6895 36.4508 14.8604C40.4167 8.03137 49.1678 5.70994 55.9969 9.67579C62.826 13.6416 65.1474 22.3928 61.1816 29.2219ZM59.1509 28.0425C60.9098 25.0135 61.1892 21.5318 60.2103 18.4354C59.3451 15.6977 57.4964 13.2614 54.8175 11.706C49.1098 8.39129 41.7957 10.3316 38.4814 16.039C37.464 17.7911 36.9417 19.6945 36.8714 21.5869V21.5873C36.7128 25.8601 38.8587 30.0782 42.8144 32.3755C48.5222 35.6906 55.8362 33.7503 59.1509 28.0425Z" fill="url(#paint1_linear_148:28)"/>
                <path opacity="0.3" d="M60.2104 18.4359C60.2104 18.4359 47.7639 8.98528 36.8715 21.5878C36.8715 21.5878 36.8715 21.5878 36.8715 21.5874C36.9418 19.695 37.4641 17.7916 38.4815 16.0395C41.7962 10.3321 49.1103 8.39178 54.8181 11.7065C57.4965 13.2619 59.3452 15.6982 60.2104 18.4359Z" fill="#C2C2C2"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M74.7302 7.03943L74.0473 7.58579L73.3644 7.03943H74.7302ZM76.7218 7.99658V9.4264H75.9577V8.85332H75.7667V12.3873H72.3282V8.85332H72.1372V9.4264H71.3731V7.99658C71.3723 7.92047 71.4175 7.8514 71.4877 7.82178L72.7132 7.42471C72.7221 7.42187 72.7314 7.42047 72.7408 7.42061H72.7694L73.6019 8.16065C73.6397 8.19428 73.6972 8.19255 73.7329 8.15674L74.0474 7.84221L74.3619 8.15674C74.3976 8.19255 74.4551 8.19428 74.4929 8.16065L75.3254 7.42061H75.3541C75.3642 7.42051 75.3743 7.42219 75.3839 7.4255L76.6018 7.81987C76.6744 7.84832 76.7221 7.91851 76.7218 7.99658ZM75.1603 7.31312L74.9857 7.08016L74.1908 7.71607L74.4338 7.95895L75.1603 7.31312ZM73.6618 7.95895L72.9353 7.31312L73.11 7.08016L73.9048 7.71607L73.6618 7.95895Z" fill="url(#paint2_linear_148:28)"/>
                <path d="M83.3015 18.2706C83.3015 18.7141 82.9407 19.0748 82.4973 19.0748C82.0539 19.0748 81.6933 18.7141 81.6933 18.2706C81.6933 17.8272 82.0539 17.4665 82.4973 17.4665C82.9407 17.4665 83.3015 17.8273 83.3015 18.2706ZM84.9904 17.1288V19.4128C84.9904 19.7173 84.7436 19.9642 84.4391 19.9642H80.5555C80.251 19.9642 80.0042 19.7173 80.0042 19.4128V17.1288C80.0042 16.8243 80.251 16.5774 80.5555 16.5774H81.2337V16.3866C81.2337 16.1202 81.4496 15.9042 81.7161 15.9042H83.2784C83.545 15.9042 83.7609 16.1202 83.7609 16.3866V16.5773H84.4391C84.7436 16.5774 84.9904 16.8243 84.9904 17.1288ZM83.715 18.2706C83.715 17.5992 83.1687 17.053 82.4973 17.053C81.826 17.053 81.2797 17.5992 81.2797 18.2706C81.2797 18.9421 81.826 19.4883 82.4973 19.4883C83.1687 19.4883 83.715 18.9421 83.715 18.2706Z" fill="url(#paint3_linear_148:28)"/>
                <path d="M12.5383 21.503H10.3373C9.90275 21.503 9.55371 21.852 9.55371 22.2865V26.9879C9.55371 27.4224 9.90987 27.7714 10.3373 27.7714H12.5383C12.9729 27.7714 13.3219 27.4224 13.3219 26.9879V22.2865C13.3219 21.852 12.9729 21.503 12.5383 21.503ZM11.4414 27.2657C11.2419 27.2657 11.0852 27.109 11.0852 26.9095C11.0852 26.7101 11.2419 26.5534 11.4414 26.5534C11.6408 26.5534 11.7975 26.7101 11.7975 26.9095C11.7975 27.109 11.6408 27.2657 11.4414 27.2657ZM12.9657 26.1758H9.90987V22.5501H12.9657V26.1758Z" fill="url(#paint4_linear_148:28)"/>
                <path d="M79.8434 26.6023L79.7754 26.6703C79.5561 26.8896 79.2891 27.0537 78.9983 27.1514C78.8075 27.2155 78.6584 27.3823 78.6584 27.5835L78.6584 27.7746C78.6584 27.8936 78.5619 27.9901 78.4429 27.9901C78.3239 27.9901 78.2275 27.8936 78.2275 27.7746L78.2275 27.4551C78.2275 27.3591 78.145 27.2839 78.0494 27.2926C77.9654 27.3003 77.9011 27.3708 77.9011 27.4551V27.8779C77.9011 27.9969 77.8047 28.0933 77.6857 28.0933C77.5667 28.0933 77.4702 27.9969 77.4702 27.8779L77.4702 27.5244C77.4702 27.4285 77.3878 27.3532 77.2922 27.362C77.2082 27.3696 77.1439 27.4401 77.1439 27.5244L77.1439 27.9811C77.1439 28.1001 77.0474 28.1966 76.9284 28.1966C76.8094 28.1966 76.7129 28.1001 76.7129 27.9811L76.7129 27.8236C76.7129 27.6042 76.5245 27.4322 76.306 27.4522L76.0788 27.473C75.999 27.4803 75.9238 27.5156 75.8671 27.5723C75.7331 27.7063 75.7331 27.9243 75.8671 28.0584L75.9994 28.1907C76.4357 28.627 76.4357 29.3369 75.9994 29.7732C75.6932 30.0794 75.2323 30.1816 74.8254 30.0334L74.7082 29.9908C74.6077 29.9542 74.4939 29.9794 74.4182 30.0551C74.3105 30.1628 74.3105 30.3382 74.4182 30.446C75.1378 31.1655 76.3043 31.1655 77.0239 30.446L80.084 27.3858C80.5317 26.9381 80.2911 26.1546 79.8434 26.6023Z" fill="url(#paint5_linear_148:28)"/>
                <path d="M76.7136 31.3658C76.4241 31.6553 76.6176 32.0924 76.9071 31.8029L80.9754 27.7346C81.265 27.4451 80.8278 27.2516 80.5383 27.5411L76.7136 31.3658Z" fill="url(#paint6_linear_148:28)"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.3912 35.6451C17.4601 35.9206 17.64 36.1509 17.8451 36.3473C18.1757 36.664 18.4113 37.0789 18.5043 37.5455C18.529 37.6693 18.6332 37.7653 18.7594 37.7653H18.7767C18.9027 37.7653 19.0048 37.8675 19.0048 37.9935C19.0048 38.1194 18.9027 38.2216 18.7767 38.2216H18.7594C18.6332 38.2216 18.529 38.3177 18.5043 38.4414C18.4113 38.908 18.1757 39.3229 17.8451 39.6395C17.64 39.836 17.4601 40.0663 17.3912 40.3418C17.267 40.8386 16.8207 41.187 16.3087 41.187H16.2271C15.7146 41.187 15.2678 40.8382 15.1435 40.341C15.0746 40.0653 14.8946 39.8349 14.6894 39.6383C14.2564 39.2235 13.9863 38.6405 13.9863 37.9935C13.9863 37.3464 14.2564 36.7634 14.6894 36.3486C14.8946 36.152 15.0746 35.9216 15.1435 35.6459C15.2678 35.1487 15.7145 34.7999 16.2271 34.7999H16.3087C16.8207 34.7999 17.267 35.1483 17.3912 35.6451ZM14.4426 37.9935C14.4426 38.9997 15.2612 39.8184 16.2675 39.8184C17.2737 39.8184 18.0924 38.9997 18.0924 37.9935C18.0924 36.9872 17.2737 36.1685 16.2675 36.1685C15.2612 36.1685 14.4426 36.9872 14.4426 37.9935ZM16.2674 36.8529C16.3934 36.8529 16.4956 36.955 16.4956 37.081V37.8049C16.4956 37.9391 16.5364 38.0702 16.6127 38.1806L16.8592 38.5377C16.9304 38.6408 16.9046 38.7821 16.8016 38.8533C16.6985 38.9246 16.5573 38.8989 16.4859 38.796L16.1766 38.3496C16.0872 38.2206 16.0393 38.0675 16.0393 37.9106V37.081C16.0393 36.955 16.1415 36.8529 16.2674 36.8529Z" fill="url(#paint7_linear_148:28)"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9305 7.9726H21.7069V8.23139H20.9305V7.9726ZM19.119 7.97283H20.2836V8.23163H19.119V7.97283ZM19.1189 13.666H20.0221L20.3134 9.8759C20.3174 9.82408 20.3606 9.78408 20.4126 9.78408C20.4646 9.78408 20.5078 9.82408 20.5118 9.8759L20.8037 13.666H21.7069V9.0793C21.4017 9.03492 21.162 8.79522 21.1176 8.49004H20.9106V8.98773C20.9106 9.15264 20.7769 9.28635 20.612 9.28635H20.4129C20.3579 9.28635 20.3134 9.24178 20.3134 9.18681V8.49004H19.7082C19.6638 8.79522 19.4241 9.03492 19.1189 9.0793V13.666ZM19.5071 8.49004C19.4669 8.68538 19.3142 8.83799 19.1189 8.87824V8.49004H19.5071ZM21.3189 8.49004H21.7071V8.87824C21.5117 8.83799 21.3591 8.68538 21.3189 8.49004ZM20.5423 7.9726H20.6717V8.23139H20.5423V7.9726ZM20.6717 8.49004V8.92137C20.6717 8.96901 20.6428 9.00764 20.607 9.00764H20.5423V8.49004H20.6717Z" fill="url(#paint8_linear_148:28)"/>
                <g opacity="0.19">
                    <path d="M20.4269 24.5356C19.7054 24.5356 19.1184 25.1226 19.1184 25.8441C19.1184 26.7395 20.2894 28.0541 20.3393 28.1096C20.3861 28.1618 20.4679 28.1617 20.5146 28.1096C20.5645 28.0541 21.7355 26.7395 21.7355 25.8441C21.7355 25.1226 21.1485 24.5356 20.4269 24.5356ZM20.4269 26.5025C20.0639 26.5025 19.7686 26.2071 19.7686 25.8441C19.7686 25.4811 20.0639 25.1858 20.4269 25.1858C20.79 25.1858 21.0853 25.4811 21.0853 25.8441C21.0853 26.2071 20.79 26.5025 20.4269 26.5025Z" fill="url(#paint9_linear_148:28)"/>
                </g>
                <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M80.8569 40.0353L82.4724 41.4733C82.5124 41.509 82.5711 41.5137 82.6161 41.485L84.0882 40.5515C84.1199 40.5313 84.1404 40.4976 84.1434 40.4601C84.1464 40.4227 84.1318 40.386 84.1036 40.361L81.924 38.4208L82.1161 38.0763C82.1435 38.0272 82.133 37.9658 82.0911 37.9285L81.5486 37.4456C81.4992 37.4016 81.4236 37.406 81.3796 37.4554C81.3356 37.5049 81.34 37.5805 81.3895 37.6245L81.8602 38.0435L80.8897 39.784C80.7579 39.7407 80.607 39.7759 80.509 39.8859C80.3774 40.0339 80.3906 40.2613 80.5385 40.393L80.7095 40.5452C80.5614 40.4211 80.3398 40.4365 80.2106 40.5817C80.0789 40.7296 80.0921 40.9571 80.2401 41.0888C80.388 41.2204 80.6154 41.2072 80.7471 41.0593C80.8764 40.9141 80.866 40.6922 80.7256 40.5595L82.154 41.831C82.2035 41.875 82.2791 41.8706 82.3231 41.8212C82.367 41.7718 82.3627 41.6962 82.3132 41.6522L80.6977 40.2141C80.6484 40.1703 80.6439 40.0945 80.6879 40.0451C80.7318 39.9958 80.8076 39.9915 80.8569 40.0353ZM81.4976 42.2083C81.3497 42.0767 81.3364 41.8492 81.4681 41.7013C81.5998 41.5534 81.8272 41.5401 81.9751 41.6718C82.1231 41.8035 82.1363 42.0309 82.0046 42.1789C81.8729 42.3268 81.6455 42.34 81.4976 42.2083Z" fill="url(#paint10_linear_148:28)"/>
                <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M50.5924 0.594578L51.8605 2.46695C51.9517 2.60162 51.9164 2.78471 51.7818 2.87591L48.6261 5.01307C48.4914 5.10428 48.3083 5.06903 48.2171 4.93438L46.9491 3.06201C46.8579 2.92735 46.8931 2.74425 47.0278 2.65304L50.1834 0.51588C50.3181 0.424678 50.5012 0.459919 50.5924 0.594578ZM48.0893 2.54882L47.4578 2.97645L47.623 3.22028L48.2544 2.79265L48.0893 2.54882ZM47.9738 3.73828L48.8753 3.12774L48.7102 2.88391L47.8086 3.49445L47.9738 3.73828ZM51.1174 2.74807L51.3612 2.58294L51.1961 2.33911L50.9523 2.50424L51.1174 2.74807ZM50.9849 0.649037L51.0152 0.628483C51.1499 0.537281 51.333 0.572514 51.4242 0.707181L52.7292 2.63412C52.8204 2.76879 52.7851 2.95188 52.6505 3.04308L49.4948 5.18025C49.3601 5.27145 49.177 5.23622 49.0858 5.10155L49.0653 5.0712L51.9468 3.11972C52.2157 2.93761 52.2863 2.57069 52.1042 2.3018L50.9849 0.649037Z" fill="url(#paint11_linear_148:28)"/>
                <path opacity="0.2" fill-rule="evenodd" clip-rule="evenodd" d="M50.6387 44.8487C50.5577 44.8075 50.4586 44.8398 50.4174 44.9208L49.1935 47.3281C49.1523 47.4091 49.1846 47.5082 49.2656 47.5494C49.2895 47.5615 49.315 47.5673 49.34 47.5673C49.4 47.5673 49.4578 47.5344 49.4869 47.4773L50.7108 45.07C50.752 44.989 50.7197 44.8899 50.6387 44.8487ZM49.7597 45.6415C49.7597 45.2974 49.4797 45.0174 49.1356 45.0174C48.7915 45.0174 48.5115 45.2974 48.5115 45.6415C48.5115 45.9857 48.7915 46.2657 49.1356 46.2657C49.4798 46.2657 49.7597 45.9857 49.7597 45.6415ZM49.1356 45.9365C48.9729 45.9365 48.8406 45.8042 48.8406 45.6415C48.8406 45.4789 48.9729 45.3465 49.1356 45.3465C49.2983 45.3465 49.4306 45.4789 49.4306 45.6415C49.4306 45.8042 49.2983 45.9365 49.1356 45.9365ZM50.152 46.7566C50.152 46.4125 50.432 46.1325 50.7761 46.1325C51.1203 46.1325 51.4002 46.4125 51.4002 46.7566C51.4002 47.1008 51.1202 47.3808 50.7761 47.3808C50.4319 47.3808 50.152 47.1008 50.152 46.7566ZM50.4811 46.7566C50.4811 46.9193 50.6134 47.0516 50.7761 47.0516C50.9388 47.0516 51.0711 46.9193 51.0711 46.7566C51.0711 46.594 50.9388 46.4616 50.7761 46.4616C50.6134 46.4616 50.4811 46.594 50.4811 46.7566Z" fill="url(#paint12_linear_148:28)"/>
                <path opacity="0.2" d="M23.7631 42.5336C23.8592 42.6265 23.981 42.6921 24.1252 42.7285C24.1798 42.7423 24.2346 42.7067 24.2477 42.6491C24.2607 42.5915 24.227 42.5336 24.1724 42.5198C24.0623 42.492 23.9708 42.4435 23.9005 42.3755C23.8311 42.3084 23.7805 42.2186 23.7499 42.1087C23.7369 42.0619 23.6963 42.0316 23.6524 42.0316C23.6429 42.0316 23.6333 42.033 23.6237 42.036C23.5698 42.0527 23.539 42.1123 23.5548 42.1691C23.5959 42.3171 23.666 42.4397 23.7631 42.5336Z" fill="url(#paint13_linear_148:28)"/>
                <path opacity="0.2" d="M76.9927 12.1362C76.8967 12.0433 76.7749 11.9778 76.6307 11.9414C76.5761 11.9276 76.5212 11.9632 76.5082 12.0208C76.4951 12.0784 76.5288 12.1363 76.5834 12.1501C76.6936 12.1778 76.7851 12.2264 76.8553 12.2944C76.9247 12.3615 76.9754 12.4513 77.006 12.5612C77.019 12.6079 77.0595 12.6383 77.1035 12.6383C77.113 12.6383 77.1226 12.6368 77.1322 12.6339C77.186 12.6172 77.2169 12.5576 77.2011 12.5007C77.1599 12.3528 77.0898 12.2302 76.9927 12.1362Z" fill="url(#paint14_linear_148:28)"/>
                <g opacity="0.5">
                    <circle opacity="0.5" cx="10.9534" cy="11.9385" r="0.46656" fill="#FDBC00"/>
                    <circle cx="0.572576" cy="21.8529" r="0.34992" fill="#50D9B2"/>
                    <circle opacity="0.5" cx="3.37189" cy="36.0833" r="0.34992" fill="#3D8DFF"/>
                </g>
                <g opacity="0.5">
                    <circle opacity="0.5" r="0.46656" transform="matrix(-1 0 0 1 94.4683 14.7377)" fill="#FDBC00"/>
                    <circle r="0.34992" transform="matrix(-1 0 0 1 100.65 26.285)" fill="#50D9B2"/>
                    <circle opacity="0.5" r="0.34992" transform="matrix(-1 0 0 1 94.8178 36.316)" fill="#3D8DFF"/>
                </g>
            </g>
            <defs>
                <linearGradient id="paint0_linear_148:28" x1="78.0283" y1="47.0891" x2="60.991" y2="33.1147" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#72D0FF"/>
                    <stop offset="1" stop-color="#349EFA"/>
                </linearGradient>
                <linearGradient id="paint1_linear_148:28" x1="63.1177" y1="7.73969" x2="63.1177" y2="36.3426" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#675FE5"/>
                    <stop offset="1" stop-color="#40408D"/>
                </linearGradient>
                <linearGradient id="paint2_linear_148:28" x1="71.373" y1="7.03943" x2="71.373" y2="12.3874" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#72D0FF"/>
                    <stop offset="1" stop-color="#349EFA"/>
                </linearGradient>
                <linearGradient id="paint3_linear_148:28" x1="80.0042" y1="15.9042" x2="80.0042" y2="19.9642" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FEE5AC"/>
                    <stop offset="1" stop-color="#FFB181"/>
                </linearGradient>
                <linearGradient id="paint4_linear_148:28" x1="9.55371" y1="21.503" x2="9.55371" y2="27.7714" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#D7CBFF"/>
                    <stop offset="1" stop-color="#5F58FF"/>
                </linearGradient>
                <linearGradient id="paint5_linear_148:28" x1="78.2227" y1="25.2166" x2="80.329" y2="29.4633" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#61FA83"/>
                    <stop offset="1" stop-color="#0CC8BC"/>
                </linearGradient>
                <linearGradient id="paint6_linear_148:28" x1="80.5016" y1="27.5778" x2="81.0237" y2="28.1594" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#61FA83"/>
                    <stop offset="1" stop-color="#0CC8BC"/>
                </linearGradient>
                <linearGradient id="paint7_linear_148:28" x1="16.4956" y1="41.187" x2="16.4956" y2="34.7999" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#A75BFF"/>
                    <stop offset="1" stop-color="#E2CBFF"/>
                </linearGradient>
                <linearGradient id="paint8_linear_148:28" x1="20.4129" y1="9.0522" x2="22.5279" y2="11.2175" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FEBADB"/>
                    <stop offset="1" stop-color="#FF5DE1"/>
                </linearGradient>
                <linearGradient id="paint9_linear_148:28" x1="19.1184" y1="24.5356" x2="19.1184" y2="28.1487" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFBA67"/>
                    <stop offset="1" stop-color="#FF635E"/>
                </linearGradient>
                <linearGradient id="paint10_linear_148:28" x1="81.4592" y1="37.366" x2="79.0713" y2="40.0487" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFBA67"/>
                    <stop offset="1" stop-color="#FF635E"/>
                </linearGradient>
                <linearGradient id="paint11_linear_148:28" x1="46.7839" y1="2.81818" x2="48.7331" y2="5.69622" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFBA67"/>
                    <stop offset="1" stop-color="#FF635E"/>
                </linearGradient>
                <linearGradient id="paint12_linear_148:28" x1="48.5115" y1="44.8308" x2="48.5115" y2="47.5673" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#FFBA67"/>
                    <stop offset="1" stop-color="#FF635E"/>
                </linearGradient>
                <linearGradient id="paint13_linear_148:28" x1="24.2505" y1="42.7314" x2="24.2505" y2="42.0316" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#72D0FF"/>
                    <stop offset="1" stop-color="#349EFA"/>
                </linearGradient>
                <linearGradient id="paint14_linear_148:28" x1="76.5054" y1="11.9384" x2="76.5054" y2="12.6383" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#72D0FF"/>
                    <stop offset="1" stop-color="#349EFA"/>
                </linearGradient>
            </defs>
        </svg>
        <p class="main-header"><?php esc_html_e( 'Search similar products in this marketplace', 'dokan' ); ?></p>
        <p class="sub-header"><?php esc_html_e( 'to duplicate products image, content, attributes, tags etc', 'dokan' ); ?></p>
    </div>
    <form action="<?php echo esc_url( $action ); ?>" type="GET" class="product-search-form dokan-form-inline">
        <div class="row">
            <div class="col-md-12">
                <div class="dokan-input-group input-group-center">
                    <input type="text" name="search" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Search Product', 'dokan' ); ?>">
                    <span class="dokan-input-group-btn">
                        <input class="dokan-btn dokan-btn-search" type="submit" value="<?php esc_attr_e( 'Search', 'dokan' ); ?>">
                    </span>
                </div><!-- /input-group -->
            </div><!-- /.col-md-12 -->
        </div><!-- /.row -->
        <input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>">
    </form>
</div>
