import React from 'react'
import ExternalLink from '../Buttons/ExternalLink'

import styles from './FooterBar.module.scss'

const FooterBar = () => {
  return (
    <div className={styles.footerBar}>
      <p>
        <strong>Feedback &amp; Support: </strong> If you have any questions or feedback about the plugin, please{' '}
        <ExternalLink href='https://elementorkit.site/contact-us/' target='_blank' text='contact us' />{'.'}
      </p>
    </div>
  )
}

export default FooterBar
