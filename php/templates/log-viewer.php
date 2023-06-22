<?php
namespace WooCommerce_Groovy_Logs;

/**
 * @var Log_Record[] $logs
 */
?>
<div id="groovy-logs-viewer">

	<section class="search">
		<input type="text" name="log-search" placeholder="<?php esc_attr_e( 'Search', 'woocommerce-groovy-logs' ); ?>" />
	</section>

    <table>
        <thead>
            <tr>
                <th></th>
                <th><?php esc_html_e( 'Date', 'woocommerce-groovy-logs' ); ?></th>
                <th><?php esc_html_e( 'Level', 'woocommerce-groovy-logs' ); ?></th>
                <th><?php esc_html_e( 'Message', 'woocommerce-groovy-logs' ); ?></th>
                <th><?php esc_html_e( 'Context', 'woocommerce-groovy-logs' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $logs as $log ): ?>
                <tr>
                    <td></td>
                    <td><?php echo $log->timestamp; ?></td>
                    <td><?php echo $log->level; ?></td>
                    <td><?php echo $log->message; ?></td>
                    <td><?php echo $log->context; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>