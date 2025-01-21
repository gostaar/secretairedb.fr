import { linkMenu as userLinkMenu } from './user/linkMenu.js';
import { linkMenu as siteLinkMenu } from './site/linkMenu.js';

export function initializeMenu(path) {
    if (path.startsWith('/user')) {
        userLinkMenu();
    } else if (path.startsWith('/')) {
        siteLinkMenu();
    }
}