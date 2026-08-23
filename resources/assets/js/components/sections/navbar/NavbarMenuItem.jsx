import React, { useState, useRef, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { isMenuActive } from './useNavbarEffects';

/**
 * NavbarMenuItem — reusable menu item with dropdown submenu support.
 * 
 * Props:
 * - item: { id, title, url, target, children? }
 * - onToggle: function to close mobile menu (optional)
 * - isMobile: boolean (default false)
 */
export default function NavbarMenuItem({ item, onToggle, isMobile = false }) {
    const { url } = usePage();
    const [open, setOpen] = useState(false);
    const dropdownRef = useRef(null);
    const hasChildren = item.children && item.children.length > 0;
    const active = isMenuActive(item.url, url);

    // Close dropdown when clicking outside
    useEffect(() => {
        function handleClickOutside(e) {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
                setOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Mobile: simple link or toggle
    if (isMobile) {
        if (!hasChildren) {
            return item.target === '_blank'
                ? <a href={item.url} target="_blank" rel="noreferrer" className={active ? 'nav-active' : ''} onClick={onToggle}>{item.title}</a>
                : <Link href={item.url} onClick={onToggle} className={active ? 'nav-active' : ''}>{item.title}</Link>;
        }

        return (
            <div className={`nav-dropdown ${open ? 'nav-dropdown--open' : ''}`}>
                <button className="nav-dropdown-toggle" onClick={() => setOpen(!open)}>
                    {item.title}
                    <ChevronDown size={14} className={`nav-dropdown-chevron ${open ? 'rotated' : ''}`} />
                </button>
                {open && (
                    <div className="nav-dropdown-menu">
                        {item.children.map(child => (
                            <NavbarMenuItem key={child.id} item={child} onToggle={onToggle} isMobile />
                        ))}
                    </div>
                )}
            </div>
        );
    }

    // Desktop: no children → simple link
    if (!hasChildren) {
        return item.target === '_blank'
            ? <a href={item.url} target="_blank" rel="noreferrer" className={active ? 'nav-active' : ''}>{item.title}</a>
            : <Link href={item.url} className={active ? 'nav-active' : ''}>{item.title}</Link>;
    }

    // Desktop: has children → dropdown
    return (
        <div className="nav-dropdown" ref={dropdownRef} onMouseEnter={() => setOpen(true)} onMouseLeave={() => setOpen(false)}>
            <button className={`nav-dropdown-trigger ${active ? 'nav-active' : ''}`} onClick={() => setOpen(!open)}>
                {item.title}
                <ChevronDown size={14} className={`nav-dropdown-chevron ${open ? 'rotated' : ''}`} />
            </button>
            {open && (
                <div className="nav-dropdown-menu">
                    {item.children.map(child => (
                        <NavbarMenuItem key={child.id} item={child} onToggle={onToggle} isMobile={false} />
                    ))}
                </div>
            )}
        </div>
    );
}
