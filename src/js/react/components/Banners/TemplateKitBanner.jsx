import React, { useEffect, useState } from 'react'
import useGlobalConfig from '../Contexts/useGlobalConfig'
import styles from './TemplateKitBanner.module.scss'
import ActivateButton from '../Buttons/ActivateButton'
import DismissBannerButton, { isBannerAlreadyDismissed } from '../Buttons/DismissBanner'
import ExternalLinkButton from '../Buttons/ExternalLinkButton'
import MainHeading from '../Titles/MainHeading'
import ButtonWrapper from '../Buttons/ButtonWrapper'

const TemplateKitBanner = () => {
  const bannerId = 'templateKitBanner'
  const { subscriptionStatus } = useGlobalConfig()
  // Figure out if banner is already dismissed
  const alreadyDismissed = isBannerAlreadyDismissed(bannerId)
  // Figure out if we should show the banner or not
  const shouldWeShowTheBanner = !alreadyDismissed && subscriptionStatus !== 'paid'
  // Use local state here to decide if banner should display or not
  const [showBanner, setShowBanner] = useState(shouldWeShowTheBanner)

  useEffect(() => {
    // If our subscription status is updated from elsewhere, we need to close this modal.
    if (subscriptionStatus === 'paid') {
      setShowBanner(false)
    }
  }, [subscriptionStatus])

  if (!showBanner) {
    // If our banner is dismissed we return nothing for this component
    return null
  }

  return (
    <div className={styles.wrapper}>
      <div className={styles.col}>
        <MainHeading title='Premium Template Kits from ElementorKit' />
        <div className={styles.text}>Start with one free template kit and see how easy it is to build! When you’re ready for more, unlock hundreds of stunning Premium Template Kits with an ElementorKit subscription.</div>
        <ButtonWrapper>
          <ActivateButton />
          <ExternalLinkButton type='ghost' label='Find out more about ElementorKit' icon='arrow' href='https://elementorkit.site' openNewWindow='true' />
        </ButtonWrapper>
      </div>
      <div className={styles.col}>
        <div className={styles.colRight} />
      </div>
      <div className={styles.dismiss}>
        <DismissBannerButton
          bannerId={bannerId} completeCallback={() => {
            // Once our dismiss action has completed we update our local state to hide our banner.
            setShowBanner(false)
          }}
        />
      </div>
    </div>
  )
}

export default TemplateKitBanner
