D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ u s e r s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ s u r v e y s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ s a m p l e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ s a m p l e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ o c c u r r e n c e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ o c c u r r e n c e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ l o c a t i o n s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ l o c a t i o n s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ s a m p l e _ a t t r i b u t e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ l o c a t i o n _ a t t r i b u t e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ o c c u r r e n c e _ a t t r i b u t e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ o c c u r r e n c e _ a t t r i b u t e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ t e r m l i s t s _ t e r m s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ t e r m l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ t a x o n _ l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ t a x a _ t a x o n _ l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ s u r v e y s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ p e o p l e ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ w e b s i t e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ w e b s i t e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ t e r m l i s t s _ t e r m s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ t e r m l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ t e r m s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ t e r m s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ t a x o n _ l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ t a x o n _ g r o u p s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ t a x o n _ g r o u p s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ t a x a _ t a x o n _ l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ s u r v e y s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ p e o p l e ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . d e t a i l _ l a n g u a g e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . l i s t _ l a n g u a g e s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ t a x o n _ l i s t s _ t a x a ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ t e r m l i s t s _ t e r m s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ t e r m _ t e r m l i s t s ;  
 D R O P   V I E W   I F   E X I S T S   i _ s c h e m a . g v _ t e r m l i s t s ;  
 S E T   c h e c k _ f u n c t i o n _ b o d i e s   =   f a l s e ;  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ t e r m l i s t s   ( O I D   =   7 2 3 0 0 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ t e r m l i s t s   A S  
 S E L E C T   t . i d ,   t . t i t l e ,   t . d e s c r i p t i o n ,   t . w e b s i t e _ i d ,   t . p a r e n t _ i d ,   t . d e l e t e d ,  
         t . c r e a t e d _ o n ,   t . c r e a t e d _ b y _ i d ,   t . u p d a t e d _ o n ,   t . u p d a t e d _ b y _ i d ,   w . t i t l e  
         A S   w e b s i t e ,   p . s u r n a m e   A S   c r e a t o r  
 F R O M   ( ( ( t e r m l i s t s   t   L E F T   J O I N   w e b s i t e s   w   O N   ( ( t . w e b s i t e _ i d   =   w . i d ) ) )   J O I N  
         u s e r s   u   O N   ( ( t . c r e a t e d _ b y _ i d   =   u . i d ) ) )   J O I N   p e o p l e   p   O N   ( ( u . p e r s o n _ i d   =  
         p . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ t e r m _ t e r m l i s t s   ( O I D   =   7 2 3 4 5 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ t e r m _ t e r m l i s t s   A S  
 S E L E C T   t t . i d ,   t t . t e r m l i s t _ i d ,   t t . t e r m _ i d ,   t t . c r e a t e d _ o n ,   t t . c r e a t e d _ b y _ i d ,  
         t t . u p d a t e d _ o n ,   t t . u p d a t e d _ b y _ i d ,   t t . p a r e n t _ i d ,   t t . m e a n i n g _ i d ,  
         t t . p r e f e r r e d ,   t t . s o r t _ o r d e r ,   t . t i t l e ,   t . d e s c r i p t i o n  
 F R O M   ( t e r m l i s t s _ t e r m s   t t   J O I N   t e r m l i s t s   t   O N   ( ( t t . t e r m l i s t _ i d   =   t . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ t e r m l i s t s _ t e r m s   ( O I D   =   7 2 3 9 8 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ t e r m l i s t s _ t e r m s   A S  
 S E L E C T   t t . i d ,   t t . t e r m l i s t _ i d ,   t t . t e r m _ i d ,   t t . c r e a t e d _ o n ,   t t . c r e a t e d _ b y _ i d ,  
         t t . u p d a t e d _ o n ,   t t . u p d a t e d _ b y _ i d ,   t t . p a r e n t _ i d ,   t t . m e a n i n g _ i d ,  
         t t . p r e f e r r e d ,   t t . s o r t _ o r d e r ,   t t . d e l e t e d ,   t . t e r m ,   l . l a n g u a g e  
 F R O M   ( ( t e r m l i s t s _ t e r m s   t t   J O I N   t e r m s   t   O N   ( ( t t . t e r m _ i d   =   t . i d ) ) )   J O I N  
         l a n g u a g e s   l   O N   ( ( t . l a n g u a g e _ i d   =   l . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ t a x o n _ l i s t s _ t a x a   ( O I D   =   7 2 4 0 2 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ t a x o n _ l i s t s _ t a x a   A S  
 S E L E C T   t t . i d ,   t t . t a x o n _ l i s t _ i d ,   t t . t a x o n _ i d ,   t t . c r e a t e d _ o n ,  
         t t . c r e a t e d _ b y _ i d ,   t t . p a r e n t _ i d ,   t t . t a x o n _ m e a n i n g _ i d ,  
         t t . t a x o n o m i c _ s o r t _ o r d e r ,   t t . p r e f e r r e d ,   t t . d e l e t e d ,   t . t a x o n ,  
         t . t a x o n _ g r o u p _ i d ,   t . l a n g u a g e _ i d ,   t . a u t h o r i t y ,   t . s e a r c h _ c o d e ,  
         t . s c i e n t i f i c ,   l . l a n g u a g e  
 F R O M   ( ( t a x a _ t a x o n _ l i s t s   t t   J O I N   t a x a   t   O N   ( ( t t . t a x o n _ i d   =   t . i d ) ) )   J O I N  
         l a n g u a g e s   l   O N   ( ( t . l a n g u a g e _ i d   =   l . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ l a n g u a g e s   ( O I D   =   7 2 4 1 6 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ l a n g u a g e s   A S  
 S E L E C T   l . i d ,   l . l a n g u a g e ,   l . i s o  
 F R O M   l a n g u a g e s   l ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ l a n g u a g e s   ( O I D   =   7 2 4 2 0 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ l a n g u a g e s   A S  
 S E L E C T   l . i d ,   l . l a n g u a g e ,   l . i s o ,   l . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S   c r e a t e d _ b y ,  
         l . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( l a n g u a g e s   l   J O I N   u s e r s   c   O N   ( ( c . i d   =   l . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u  
         O N   ( ( u . i d   =   l . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ p e o p l e   ( O I D   =   7 2 4 3 8 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ p e o p l e   A S  
 S E L E C T   p . i d ,   p . f i r s t _ n a m e ,   p . s u r n a m e ,   p . i n i t i a l s ,   p . e m a i l _ a d d r e s s ,  
         p . w e b s i t e _ u r l ,   p . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S   c r e a t e d _ b y ,  
         p . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( p e o p l e   p   J O I N   u s e r s   c   O N   ( ( c . i d   =   p . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N  
         ( ( u . i d   =   p . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ s u r v e y s   ( O I D   =   7 2 4 4 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ s u r v e y s   A S  
 S E L E C T   s . i d ,   s . t i t l e ,   s . o w n e r _ i d ,   p . s u r n a m e   A S   o w n e r ,   s . d e s c r i p t i o n ,  
         s . w e b s i t e _ i d ,   w . t i t l e   A S   w e b s i t e ,   s . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S  
         c r e a t e d _ b y ,   s . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( ( s u r v e y s   s   J O I N   u s e r s   c   O N   ( ( c . i d   =   s . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u  
         O N   ( ( u . i d   =   s . u p d a t e d _ b y _ i d ) ) )   J O I N   p e o p l e   p   O N   ( ( p . i d   =   s . o w n e r _ i d ) ) )  
         J O I N   w e b s i t e s   w   O N   ( ( w . i d   =   s . w e b s i t e _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ t a x a _ t a x o n _ l i s t s   ( O I D   =   7 2 4 5 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ t a x a _ t a x o n _ l i s t s   A S  
 S E L E C T   t t l . i d ,   t t l . t a x o n _ i d ,   t . t a x o n ,   t . a u t h o r i t y ,   t t l . t a x o n _ l i s t _ i d ,  
         t l . t i t l e   A S   t a x o n _ l i s t ,   t t l . t a x o n _ m e a n i n g _ i d ,   t t l . p r e f e r r e d ,  
         t t l . p a r e n t _ i d ,   t p . t a x o n   A S   p a r e n t ,   t t l . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S  
         c r e a t e d _ b y ,   t t l . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( ( ( ( t a x a _ t a x o n _ l i s t s   t t l   J O I N   t a x o n _ l i s t s   t l   O N   ( ( t l . i d   =  
         t t l . t a x o n _ l i s t _ i d ) ) )   J O I N   t a x a   t   O N   ( ( t . i d   =   t t l . t a x o n _ i d ) ) )   J O I N   u s e r s  
         c   O N   ( ( c . i d   =   t t l . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =  
         t t l . u p d a t e d _ b y _ i d ) ) )   L E F T   J O I N   t a x a _ t a x o n _ l i s t s   t t l p   O N   ( ( t t l p . i d   =  
         t t l . p a r e n t _ i d ) ) )   L E F T   J O I N   t a x a   t p   O N   ( ( t p . i d   =   t t l p . t a x o n _ i d ) ) )  
 W H E R E   ( t t l . d e l e t e d   =   f a l s e ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ t a x o n _ g r o u p s   ( O I D   =   7 2 4 6 2 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ t a x o n _ g r o u p s   A S  
 S E L E C T   t . i d ,   t . t i t l e  
 F R O M   t a x o n _ g r o u p s   t ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ t a x o n _ g r o u p s   ( O I D   =   7 2 4 6 6 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ t a x o n _ g r o u p s   A S  
 S E L E C T   t . i d ,   t . t i t l e ,   t . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S   c r e a t e d _ b y ,  
         t . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( t a x o n _ g r o u p s   t   J O I N   u s e r s   c   O N   ( ( c . i d   =   t . c r e a t e d _ b y _ i d ) ) )   J O I N  
         u s e r s   u   O N   ( ( u . i d   =   t . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ t a x o n _ l i s t s   ( O I D   =   7 2 4 7 5 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ t a x o n _ l i s t s   A S  
 S E L E C T   t . i d ,   t . t i t l e ,   t . d e s c r i p t i o n ,   t . w e b s i t e _ i d ,   w . t i t l e   A S   w e b s i t e ,  
         t . p a r e n t _ i d ,   p . t i t l e   A S   p a r e n t ,   t . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S  
         c r e a t e d _ b y ,   t . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( ( t a x o n _ l i s t s   t   L E F T   J O I N   w e b s i t e s   w   O N   ( ( w . i d   =   t . w e b s i t e _ i d ) ) )  
         L E F T   J O I N   t a x o n _ l i s t s   p   O N   ( ( p . i d   =   t . p a r e n t _ i d ) ) )   J O I N   u s e r s   c   O N  
         ( ( c . i d   =   t . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =   t . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ t e r m s   ( O I D   =   7 2 4 8 0 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ t e r m s   A S  
 S E L E C T   t . i d ,   t . t e r m ,   t . l a n g u a g e _ i d ,   l . l a n g u a g e ,   l . i s o  
 F R O M   ( t e r m s   t   J O I N   l a n g u a g e s   l   O N   ( ( l . i d   =   t . l a n g u a g e _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ t e r m s   ( O I D   =   7 2 4 8 4 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ t e r m s   A S  
 S E L E C T   t . i d ,   t . t e r m ,   t . l a n g u a g e _ i d ,   l . l a n g u a g e ,   l . i s o ,   t . c r e a t e d _ b y _ i d ,  
         c . u s e r n a m e   A S   c r e a t e d _ b y ,   t . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( t e r m s   t   J O I N   l a n g u a g e s   l   O N   ( ( l . i d   =   t . l a n g u a g e _ i d ) ) )   J O I N   u s e r s   c  
         O N   ( ( c . i d   =   t . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =   t . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ t e r m l i s t s   ( O I D   =   7 2 4 9 3 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ t e r m l i s t s   A S  
 S E L E C T   t . i d ,   t . t i t l e ,   t . d e s c r i p t i o n ,   t . w e b s i t e _ i d ,   w . t i t l e   A S   w e b s i t e ,  
         t . p a r e n t _ i d ,   p . t i t l e   A S   p a r e n t ,   t . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S  
         c r e a t e d _ b y ,   t . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( ( t e r m l i s t s   t   L E F T   J O I N   w e b s i t e s   w   O N   ( ( w . i d   =   t . w e b s i t e _ i d ) ) )   L E F T  
         J O I N   t e r m l i s t s   p   O N   ( ( p . i d   =   t . p a r e n t _ i d ) ) )   J O I N   u s e r s   c   O N   ( ( c . i d   =  
         t . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =   t . u p d a t e d _ b y _ i d ) ) )  
 W H E R E   ( t . d e l e t e d   =   f a l s e ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ t e r m l i s t s _ t e r m s   ( O I D   =   7 2 5 0 2 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ t e r m l i s t s _ t e r m s   A S  
 S E L E C T   t l t . i d ,   t l t . t e r m _ i d ,   t . t e r m ,   t l t . t e r m l i s t _ i d ,   t l . t i t l e   A S   t e r m l i s t ,  
         t l t . m e a n i n g _ i d ,   t l t . p r e f e r r e d ,   t l t . p a r e n t _ i d ,   t p . t e r m   A S   p a r e n t ,  
         t l t . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S   c r e a t e d _ b y ,   t l t . u p d a t e d _ b y _ i d ,  
         u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( ( ( ( t e r m l i s t s _ t e r m s   t l t   J O I N   t e r m l i s t s   t l   O N   ( ( t l . i d   =  
         t l t . t e r m l i s t _ i d ) ) )   J O I N   t e r m s   t   O N   ( ( t . i d   =   t l t . t e r m _ i d ) ) )   J O I N   u s e r s   c  
         O N   ( ( c . i d   =   t l t . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =  
         t l t . u p d a t e d _ b y _ i d ) ) )   L E F T   J O I N   t e r m l i s t s _ t e r m s   t l t p   O N   ( ( t l t p . i d   =  
         t l t . p a r e n t _ i d ) ) )   L E F T   J O I N   t e r m s   t p   O N   ( ( t p . i d   =   t l t p . t e r m _ i d ) ) )  
 W H E R E   ( t l t . d e l e t e d   =   f a l s e ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ w e b s i t e s   ( O I D   =   7 2 5 0 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ w e b s i t e s   A S  
 S E L E C T   w . i d ,   w . t i t l e  
 F R O M   w e b s i t e s   w ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ w e b s i t e s   ( O I D   =   7 2 5 1 1 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ w e b s i t e s   A S  
 S E L E C T   w . i d ,   w . t i t l e ,   w . u r l ,   w . d e s c r i p t i o n ,   w . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S  
         c r e a t e d _ b y ,   w . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( w e b s i t e s   w   J O I N   u s e r s   c   O N   ( ( c . i d   =   w . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u  
         O N   ( ( u . i d   =   w . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ p e o p l e   ( O I D   =   7 2 6 1 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ p e o p l e   A S  
 S E L E C T   p . i d ,   p . f i r s t _ n a m e ,   p . s u r n a m e ,   p . i n i t i a l s ,   ( ( ( p . f i r s t _ n a m e ) : : t e x t   | |  
         '   ' : : t e x t )   | |   ( p . s u r n a m e ) : : t e x t )   A S   c a p t i o n  
 F R O M   p e o p l e   p ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ s u r v e y s   ( O I D   =   7 2 7 1 8 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ s u r v e y s   A S  
 S E L E C T   s . i d ,   s . t i t l e ,   s . w e b s i t e _ i d  
 F R O M   s u r v e y s   s ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ t a x a _ t a x o n _ l i s t s   ( O I D   =   7 2 7 2 2 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ t a x a _ t a x o n _ l i s t s   A S  
 S E L E C T   t t l . i d ,   t t l . t a x o n _ i d ,   t . t a x o n ,   t . a u t h o r i t y ,   t t l . t a x o n _ l i s t _ i d ,  
         t t l . p r e f e r r e d ,   t l . t i t l e   A S   t a x o n _ l i s t ,   t l . w e b s i t e _ i d  
 F R O M   ( ( t a x a _ t a x o n _ l i s t s   t t l   J O I N   t a x o n _ l i s t s   t l   O N   ( ( t l . i d   =  
         t t l . t a x o n _ l i s t _ i d ) ) )   J O I N   t a x a   t   O N   ( ( t . i d   =   t t l . t a x o n _ i d ) ) )  
 W H E R E   ( t t l . d e l e t e d   =   f a l s e ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ t a x o n _ l i s t s   ( O I D   =   7 2 7 2 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ t a x o n _ l i s t s   A S  
 S E L E C T   t . i d ,   t . t i t l e ,   t . w e b s i t e _ i d  
 F R O M   t a x o n _ l i s t s   t ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ t e r m l i s t s   ( O I D   =   7 2 7 3 1 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ t e r m l i s t s   A S  
 S E L E C T   t . i d ,   t . t i t l e ,   t . w e b s i t e _ i d  
 F R O M   t e r m l i s t s   t  
 W H E R E   ( t . d e l e t e d   =   f a l s e ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ t e r m l i s t s _ t e r m s   ( O I D   =   7 2 7 3 5 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ t e r m l i s t s _ t e r m s   A S  
 S E L E C T   t l t . i d ,   t l t . t e r m _ i d ,   t . t e r m ,   t l t . t e r m l i s t _ i d ,   t l . t i t l e   A S   t e r m l i s t ,  
         t l . w e b s i t e _ i d  
 F R O M   ( ( t e r m l i s t s _ t e r m s   t l t   J O I N   t e r m l i s t s   t l   O N   ( ( t l . i d   =  
         t l t . t e r m l i s t _ i d ) ) )   J O I N   t e r m s   t   O N   ( ( t . i d   =   t l t . t e r m _ i d ) ) )  
 W H E R E   ( t l t . d e l e t e d   =   f a l s e ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ o c c u r r e n c e _ a t t r i b u t e s   ( O I D   =   7 2 7 4 3 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ o c c u r r e n c e _ a t t r i b u t e s   A S  
 S E L E C T   o a . i d ,   o a . c a p t i o n ,   o a . d a t a _ t y p e ,   o a . t e r m l i s t _ i d ,   o a . m u l t i _ v a l u e ,  
         o a w . w e b s i t e _ i d ,   ( ( ( ( o a . i d   | |   ' | ' : : t e x t )   | |   ( o a . d a t a _ t y p e ) : : t e x t )   | |  
         ' | ' : : t e x t )   | |   C O A L E S C E ( ( o a . t e r m l i s t _ i d ) : : t e x t ,   ' ' : : t e x t ) )   A S   s i g n a t u r e  
 F R O M   ( o c c u r r e n c e _ a t t r i b u t e s   o a   L E F T   J O I N   o c c u r r e n c e _ a t t r i b u t e s _ w e b s i t e s   o a w  
         O N   ( ( o a w . o c c u r r e n c e _ a t t r i b u t e _ i d   =   o a . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ o c c u r r e n c e _ a t t r i b u t e s   ( O I D   =   7 2 7 5 4 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ o c c u r r e n c e _ a t t r i b u t e s   A S  
 S E L E C T   o a w . i d ,   o a w . w e b s i t e _ i d ,   o a w . r e s t r i c t _ t o _ s u r v e y _ i d   A S   s u r v e y _ i d ,  
         w . t i t l e   A S   w e b s i t e ,   s . t i t l e   A S   s u r v e y ,   o a . c a p t i o n ,   o a . d a t a _ t y p e  
 F R O M   ( ( ( o c c u r r e n c e _ a t t r i b u t e s _ w e b s i t e s   o a w   L E F T   J O I N   o c c u r r e n c e _ a t t r i b u t e s  
         o a   O N   ( ( o a . i d   =   o a w . o c c u r r e n c e _ a t t r i b u t e _ i d ) ) )   L E F T   J O I N   w e b s i t e s   w   O N  
         ( ( w . i d   =   o a w . w e b s i t e _ i d ) ) )   L E F T   J O I N   s u r v e y s   s   O N   ( ( s . i d   =  
         o a w . r e s t r i c t _ t o _ s u r v e y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ l o c a t i o n _ a t t r i b u t e s   ( O I D   =   7 2 7 5 9 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ l o c a t i o n _ a t t r i b u t e s   A S  
 S E L E C T   l a w . i d ,   l a w . w e b s i t e _ i d ,   l a w . r e s t r i c t _ t o _ s u r v e y _ i d   A S   s u r v e y _ i d ,  
         w . t i t l e   A S   w e b s i t e ,   s . t i t l e   A S   s u r v e y ,   l a . c a p t i o n ,   l a . d a t a _ t y p e  
 F R O M   ( ( ( l o c a t i o n _ a t t r i b u t e s _ w e b s i t e s   l a w   L E F T   J O I N   l o c a t i o n _ a t t r i b u t e s   l a  
         O N   ( ( l a . i d   =   l a w . l o c a t i o n _ a t t r i b u t e _ i d ) ) )   L E F T   J O I N   w e b s i t e s   w   O N  
         ( ( w . i d   =   l a w . w e b s i t e _ i d ) ) )   L E F T   J O I N   s u r v e y s   s   O N   ( ( s . i d   =  
         l a w . r e s t r i c t _ t o _ s u r v e y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ s a m p l e _ a t t r i b u t e s   ( O I D   =   7 2 7 6 4 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ s a m p l e _ a t t r i b u t e s   A S  
 S E L E C T   s a w . i d ,   s a w . w e b s i t e _ i d ,   s a w . r e s t r i c t _ t o _ s u r v e y _ i d   A S   s u r v e y _ i d ,  
         w . t i t l e   A S   w e b s i t e ,   s . t i t l e   A S   s u r v e y ,   s a . c a p t i o n ,   s a . d a t a _ t y p e  
 F R O M   ( ( ( s a m p l e _ a t t r i b u t e s _ w e b s i t e s   s a w   L E F T   J O I N   s a m p l e _ a t t r i b u t e s   s a   O N  
         ( ( s a . i d   =   s a w . s a m p l e _ a t t r i b u t e _ i d ) ) )   L E F T   J O I N   w e b s i t e s   w   O N   ( ( w . i d   =  
         s a w . w e b s i t e _ i d ) ) )   L E F T   J O I N   s u r v e y s   s   O N   ( ( s . i d   =   s a w . r e s t r i c t _ t o _ s u r v e y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ l o c a t i o n s   ( O I D   =   7 2 7 8 3 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ l o c a t i o n s   A S  
 S E L E C T   l . i d ,   l . n a m e ,   l . c o d e ,   l . p a r e n t _ i d ,   p . n a m e   A S   p a r e n t ,  
         l . c e n t r o i d _ s r e f ,   l . c e n t r o i d _ s r e f _ s y s t e m ,   l . c r e a t e d _ b y _ i d ,   c . u s e r n a m e   A S  
         c r e a t e d _ b y ,   l . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S   u p d a t e d _ b y  
 F R O M   ( ( ( l o c a t i o n s   l   J O I N   u s e r s   c   O N   ( ( c . i d   =   l . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s  
         u   O N   ( ( u . i d   =   l . u p d a t e d _ b y _ i d ) ) )   L E F T   J O I N   l o c a t i o n s   p   O N   ( ( p . i d   =  
         l . p a r e n t _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ l o c a t i o n s   ( O I D   =   7 2 7 8 8 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ l o c a t i o n s   A S  
 S E L E C T   l . i d ,   l . n a m e ,   l . c o d e ,   l . c e n t r o i d _ s r e f  
 F R O M   l o c a t i o n s   l ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ o c c u r r e n c e s   ( O I D   =   7 3 0 7 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ o c c u r r e n c e s   A S  
 S E L E C T   o . i d ,   o . c o n f i d e n t i a l ,   o . c o m m e n t ,   o . t a x a _ t a x o n _ l i s t _ i d ,   t . t a x o n ,  
         s . e n t e r e d _ s r e f ,   s . e n t e r e d _ s r e f _ s y s t e m ,   s . g e o m ,   s . l o c a t i o n _ n a m e ,  
         s . d a t e _ s t a r t ,   s . d a t e _ e n d ,   s . d a t e _ t y p e ,   s . l o c a t i o n _ i d ,   l . n a m e   A S  
         l o c a t i o n ,   l . c o d e   A S   l o c a t i o n _ c o d e ,   ( ( ( d . f i r s t _ n a m e ) : : t e x t   | |   '   ' : : t e x t )  
         | |   ( d . s u r n a m e ) : : t e x t )   A S   d e t e r m i n e r ,   o . w e b s i t e _ i d ,   o . c r e a t e d _ b y _ i d ,  
         c . u s e r n a m e   A S   c r e a t e d _ b y ,   o . c r e a t e d _ o n ,   o . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S  
         u p d a t e d _ b y ,   o . u p d a t e d _ o n  
 F R O M   ( ( ( ( ( ( ( ( o c c u r r e n c e s   o   J O I N   s a m p l e s   s   O N   ( ( s . i d   =   o . s a m p l e _ i d ) ) )   L E F T  
         J O I N   p e o p l e   d   O N   ( ( d . i d   =   o . d e t e r m i n e r _ i d ) ) )   L E F T   J O I N   l o c a t i o n s   l   O N  
         ( ( l . i d   =   s . l o c a t i o n _ i d ) ) )   J O I N   t a x a _ t a x o n _ l i s t s   t t l   O N   ( ( t t l . i d   =  
         o . t a x a _ t a x o n _ l i s t _ i d ) ) )   J O I N   t a x a   t   O N   ( ( t . i d   =   t t l . t a x o n _ i d ) ) )   L E F T  
         J O I N   s u r v e y s   s u   O N   ( ( s . s u r v e y _ i d   =   s u . i d ) ) )   J O I N   u s e r s   c   O N   ( ( c . i d   =  
         o . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =   o . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ o c c u r r e n c e s   ( O I D   =   7 3 0 8 2 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ o c c u r r e n c e s   A S  
 S E L E C T   s u . t i t l e   A S   s u r v e y ,   l . n a m e   A S   l o c a t i o n ,   s . d a t e _ s t a r t ,   s . d a t e _ e n d ,  
         s . d a t e _ t y p e ,   s . e n t e r e d _ s r e f ,   s . e n t e r e d _ s r e f _ s y s t e m ,   t . t a x o n ,   o . w e b s i t e _ i d  
 F R O M   ( ( ( ( ( o c c u r r e n c e s   o   J O I N   s a m p l e s   s   O N   ( ( o . s a m p l e _ i d   =   s . i d ) ) )   L E F T   J O I N  
         l o c a t i o n s   l   O N   ( ( s . l o c a t i o n _ i d   =   l . i d ) ) )   J O I N   t a x a _ t a x o n _ l i s t s   t t l   O N  
         ( ( o . t a x a _ t a x o n _ l i s t _ i d   =   t t l . i d ) ) )   J O I N   t a x a   t   O N   ( ( t t l . t a x o n _ i d   =  
         t . i d ) ) )   L E F T   J O I N   s u r v e y s   s u   O N   ( ( s . s u r v e y _ i d   =   s u . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   d e t a i l _ s a m p l e s   ( O I D   =   7 3 0 8 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . d e t a i l _ s a m p l e s   A S  
 S E L E C T   s . i d ,   s . e n t e r e d _ s r e f ,   s . e n t e r e d _ s r e f _ s y s t e m ,   s . g e o m ,  
         s . l o c a t i o n _ n a m e ,   s . d a t e _ s t a r t ,   s . d a t e _ e n d ,   s . d a t e _ t y p e ,   s . l o c a t i o n _ i d ,  
         l . n a m e   A S   l o c a t i o n ,   l . c o d e   A S   l o c a t i o n _ c o d e ,   s . c r e a t e d _ b y _ i d ,  
         c . u s e r n a m e   A S   c r e a t e d _ b y ,   s . c r e a t e d _ o n ,   s . u p d a t e d _ b y _ i d ,   u . u s e r n a m e   A S  
         u p d a t e d _ b y ,   s . u p d a t e d _ o n  
 F R O M   ( ( ( ( s a m p l e s   s   L E F T   J O I N   l o c a t i o n s   l   O N   ( ( l . i d   =   s . l o c a t i o n _ i d ) ) )   L E F T  
         J O I N   s u r v e y s   s u   O N   ( ( s . s u r v e y _ i d   =   s u . i d ) ) )   J O I N   u s e r s   c   O N   ( ( c . i d   =  
         s . c r e a t e d _ b y _ i d ) ) )   J O I N   u s e r s   u   O N   ( ( u . i d   =   s . u p d a t e d _ b y _ i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   l i s t _ s a m p l e s   ( O I D   =   7 3 0 9 2 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . l i s t _ s a m p l e s   A S  
 S E L E C T   s . i d ,   s u . t i t l e   A S   s u r v e y ,   l . n a m e   A S   l o c a t i o n ,   s . d a t e _ s t a r t ,  
         s . d a t e _ e n d ,   s . d a t e _ t y p e ,   s . e n t e r e d _ s r e f ,   s . e n t e r e d _ s r e f _ s y s t e m  
 F R O M   ( ( s a m p l e s   s   L E F T   J O I N   l o c a t i o n s   l   O N   ( ( s . l o c a t i o n _ i d   =   l . i d ) ) )   L E F T  
         J O I N   s u r v e y s   s u   O N   ( ( s . s u r v e y _ i d   =   s u . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ s u r v e y s   ( O I D   =   7 3 0 9 7 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ s u r v e y s   A S  
 S E L E C T   s . i d ,   s . t i t l e ,   s . d e s c r i p t i o n ,   w . t i t l e   A S   w e b s i t e ,   s . d e l e t e d  
 F R O M   ( s u r v e y s   s   L E F T   J O I N   w e b s i t e s   w   O N   ( ( s . w e b s i t e _ i d   =   w . i d ) ) ) ;  
  
 - -  
 - -   D e f i n i t i o n   f o r   v i e w   g v _ u s e r s   ( O I D   =   7 3 1 0 1 )   :    
 - -  
 C R E A T E   V I E W   i _ s c h e m a . g v _ u s e r s   A S  
 S E L E C T   p . i d   A S   p e r s o n _ i d ,   ( C O A L E S C E ( ( ( p . f i r s t _ n a m e ) : : t e x t   | |   '   ' : : t e x t ) ,  
         ' ' : : t e x t )   | |   ( p . s u r n a m e ) : : t e x t )   A S   n a m e ,   u . i d ,   u . u s e r n a m e ,   c r . t i t l e   A S  
         c o r e _ r o l e ,   p . d e l e t e d  
 F R O M   ( ( p e o p l e   p   L E F T   J O I N   u s e r s   u   O N   ( ( ( p . i d   =   u . p e r s o n _ i d )   A N D   ( u . d e l e t e d  
         =   f a l s e ) ) ) )   L E F T   J O I N   c o r e _ r o l e s   c r   O N   ( ( u . c o r e _ r o l e _ i d   =   c r . i d ) ) )  
 W H E R E   ( p . e m a i l _ a d d r e s s   I S   N O T   N U L L ) ;  
  
 - -  
 - -   C o m m e n t s  
 - -  
 C O M M E N T   O N   S C H E M A   p u b l i c   I S   ' s t a n d a r d   p u b l i c   s c h e m a ' ;  
 C O M M E N T   O N   V I E W   i _ s c h e m a . g v _ t e r m _ t e r m l i s t s   I S   ' V i e w   f o r   t h e   t e r m s   p a g e   -   s h o w s   t h e   l i s t   o f   t e r m l i s t s   t h a t   a   t e r m   b e l o n g s   t o . ' ;  
 