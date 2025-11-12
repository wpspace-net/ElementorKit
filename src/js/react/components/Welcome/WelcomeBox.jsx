import React, { useState } from 'react'
import styles from './WelcomeBox.module.scss'
import InternalLinkButton from '../Buttons/InternalLinkButton'
import ExternalLinkButton from '../Buttons/ExternalLinkButton'

const WelcomeBox = () => {
  const [playVideoEmbed, setPlayVideoEmbed] = useState(false)

  return (
    <div className={styles.wrapper}>
      <div className={styles.inner}>
        <div className={styles.contentWrapper}>
          <p className={styles.subHeading}>Welcome to the new and improved</p>
          <h1 className={styles.mainHeading}>ElementorKit WordPress Plugin</h1>
          <p className={styles.whatsNew}>
            <strong>What's new?</strong>{' '}
            Watch this video below to find out more
          </p>
          <div className={styles.videoWrapper} onClick={() => { setPlayVideoEmbed(true) }}>
            {playVideoEmbed ? <iframe className={styles.videoIframe} src='https://www.youtube.com/embed/qCK3fXgfyt0?rel=0&autoplay=1' /> : null}
          </div>
          <div className={styles.buttonWrapper}>
            <InternalLinkButton type='primary' label='Premium Template Kits' icon='arrow' href='/template-kits/premium-kits' />
            <InternalLinkButton type='primary' label='Premium Photos' icon='arrow' href='/photos' />
            <ExternalLinkButton type='primary' label='Unlimited WordPress Hosting' icon='arrow' href='https://wpspace.net/unlimited-wordpress-hosting/' openNewWindow={true} />
            <ExternalLinkButton type='primary' label='Premium Elementor Hosting' icon='arrow' href='https://wpspace.net/premium-elementor-hosting/' openNewWindow={true} />
          </div>
        </div>
      </div>
    </div>
  )
}

export default WelcomeBox
