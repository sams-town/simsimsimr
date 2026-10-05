import { Link } from 'react-router-dom';
import { APP_VERSION } from '../../data/releaseNotes';

const Footer = () => {
    return (
        <div className="dark:text-white-dark text-center p-6 pt-0 mt-auto flex flex-col items-center gap-1 text-sm">
            <span>© {new Date().getFullYear()}. THB All rights reserved.</span>
            <Link
                to="/release-notes"
                className="text-xs font-medium text-gray-400 hover:text-primary dark:text-gray-500 dark:hover:text-primary transition-colors"
            >
                v{APP_VERSION}
            </Link>
        </div>
    );
};

export default Footer;
